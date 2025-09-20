<?php

namespace Botble\Stripe\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Order;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Stripe\Http\Requests\StripePaymentCallbackRequest;
use Botble\Stripe\Services\Gateways\StripePaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeController extends BaseController
{
    public function webhook(Request $request)
    {
        $webhookSecret = get_payment_setting('webhook_secret', 'stripe');
        $signature = $request->server('HTTP_STRIPE_SIGNATURE');
        $content = $request->getContent();

        if (! $webhookSecret || ! $signature || ! $content) {
            return response()->noContent();
        }

        try {
            do_action('payment_before_making_api_request', STRIPE_PAYMENT_METHOD_NAME, (array) $content);

            $event = Webhook::constructEvent(
                $content,
                $signature,
                $webhookSecret
            );

            do_action('payment_after_api_response', STRIPE_PAYMENT_METHOD_NAME, (array) $content, $event->toArray());

            if ($event->type == 'payment_intent.succeeded') {
                /**
                 * @var PaymentIntent $paymentIntent
                 */
                $paymentIntent = $event->data->object; // @phpstan-ignore-line

                $payment = Payment::query()
                    ->where('charge_id', $paymentIntent->id)
                    ->first();

                if ($payment) {
                    $payment->status = PaymentStatusEnum::COMPLETED;
                    $payment->save();

                    do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                        'charge_id' => $payment->charge_id,
                        'order_id' => $payment->order_id,
                    ]);
                }
            }
        } catch (SignatureVerificationException $e) {
            BaseHelper::logError($e);
        }

        return response()->noContent();
    }

    public function success(
        StripePaymentCallbackRequest $request,
        StripePaymentService $stripePaymentService,
        BaseHttpResponse $response
    ) {
        try {
            $stripePaymentService->setClient();

            $sessionId = $request->input('session_id');

            do_action('payment_before_making_api_request', STRIPE_PAYMENT_METHOD_NAME, ['id' => $sessionId]);

            $session = Session::retrieve($sessionId);

            do_action('payment_after_api_response', STRIPE_PAYMENT_METHOD_NAME, ['id' => $sessionId], $session->toArray());

            if ($session->payment_status == 'paid') {
                $metadata = $session->metadata->toArray();

                $orderIds = json_decode($metadata['order_id'], true);

                $charge = PaymentIntent::retrieve($session->payment_intent);

                if (! $charge->latest_charge) {
                    return $response
                        ->setError()
                        ->setNextUrl(PaymentHelper::getCancelURL())
                        ->setMessage(__('No payment charge. Please try again!'));
                }

                $chargeId = $charge->latest_charge;

                do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                    'amount' => $metadata['amount'],
                    'currency' => strtoupper($session->currency),
                    'charge_id' => $chargeId,
                    'order_id' => $orderIds,
                    'customer_id' => Arr::get($metadata, 'customer_id'),
                    'customer_type' => Arr::get($metadata, 'customer_type'),
                    'payment_channel' => STRIPE_PAYMENT_METHOD_NAME,
                    'status' => PaymentStatusEnum::COMPLETED,
                    'payment_fee' => Arr::get($metadata, 'payment_fee', 0),
                ]);

                return $response
                    ->setNextUrl(PaymentHelper::getRedirectURL() . '?charge_id=' . $chargeId)
                    ->setMessage(__('Checkout successfully!'));
            }

            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage(__('Payment failed!'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage($exception->getMessage() ?: __('Payment failed!'));
        }
    }

    public function error(BaseHttpResponse $response)
    {
        return $response
            ->setError()
            ->setNextUrl(PaymentHelper::getCancelURL())
            ->withInput()
            ->setMessage(__('Payment failed!'));
    }

    /**
     * Create Payment Intent for Stripe
     */
    public function createIntent(Request $request, string $token, BaseHttpResponse $response)
    {
        try {
            $order = Order::where('token', $token)->first();
            
            if (!$order) {
                return $response
                    ->setError()
                    ->setMessage('Order not found!');
            }

            // Set Stripe API key
            $stripeSecretKey = get_payment_setting('secret', 'stripe') ?: env('STRIPE_SECRET_KEY');
            if (!$stripeSecretKey) {
                return $response
                    ->setError()
                    ->setMessage('Stripe secret key not configured!');
            }
            Stripe::setApiKey($stripeSecretKey);

            // Create Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($order->amount * 100), // Convert to cents
                'currency' => strtolower($order->currency_id),
                'metadata' => [
                    'order_id' => $order->id,
                    'token' => $token,
                ],
            ]);

            return $response
                ->setData([
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                ])
                ->setMessage('Payment intent created successfully!');

        } catch (Exception $exception) {
            \Log::error('Stripe createIntent error: ' . $exception->getMessage());
            return $response
                ->setError()
                ->setMessage($exception->getMessage() ?: 'Failed to create payment intent!');
        }
    }

    /**
     * Confirm Payment for Stripe
     */
    public function confirm(Request $request, string $token, BaseHttpResponse $response)
    {
        try {
            $order = Order::where('token', $token)->first();
            
            if (!$order) {
                return $response
                    ->setError()
                    ->setMessage('Order not found!');
            }

            $paymentMethodId = $request->input('payment_method_id');
            $paymentIntentId = $request->input('payment_intent_id');
            $method = $request->input('method', 'card'); // card, googlePay, applePay

            if (!$paymentMethodId || !$paymentIntentId) {
                return $response
                    ->setError()
                    ->setMessage('Payment method ID and Payment Intent ID are required!');
            }

            // Set Stripe API key
            $stripeSecretKey = get_payment_setting('secret', 'stripe') ?: env('STRIPE_SECRET_KEY');
            if (!$stripeSecretKey) {
                return $response
                    ->setError()
                    ->setMessage('Stripe secret key not configured!');
            }
            Stripe::setApiKey($stripeSecretKey);

            // Confirm the Payment Intent
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            if ($paymentIntent->status === 'requires_confirmation') {
                $paymentIntent->confirm([
                    'payment_method' => $paymentMethodId,
                ]);
            }

            // Check if payment was successful
            if ($paymentIntent->status === 'succeeded') {
                // Create payment record
                Payment::create([
                    'amount' => $order->amount,
                    'currency' => $order->currency_id,
                    'charge_id' => $paymentIntent->id,
                    'order_id' => $order->id,
                    'customer_id' => $order->user_id,
                    'customer_type' => 'Botble\Ecommerce\Models\Customer',
                    'payment_channel' => 'stripe',
                    'status' => PaymentStatusEnum::COMPLETED,
                ]);

                // Update order status
                $order->status = 'completed';
                $order->save();

                do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                    'amount' => $order->amount,
                    'currency' => $order->currency_id,
                    'charge_id' => $paymentIntent->id,
                    'order_id' => $order->id,
                    'customer_id' => $order->user_id,
                    'customer_type' => 'Botble\Ecommerce\Models\Customer',
                    'payment_channel' => 'stripe',
                    'status' => PaymentStatusEnum::COMPLETED,
                ]);

                return $response
                    ->setData([
                        'success' => true,
                        'redirect_url' => route('public.checkout.success', $token),
                    ])
                    ->setMessage('Payment completed successfully!');
            } else {
                return $response
                    ->setError()
                    ->setMessage('Payment failed: ' . $paymentIntent->last_payment_error->message ?? 'Unknown error');
            }

        } catch (Exception $exception) {
            \Log::error('Stripe confirm error: ' . $exception->getMessage());
            return $response
                ->setError()
                ->setMessage($exception->getMessage() ?: 'Payment confirmation failed!');
        }
    }

    /**
     * Test method for debugging
     */
    public function test(Request $request, string $token, BaseHttpResponse $response)
    {
        try {
            $order = Order::where('token', $token)->first();
            
            return $response
                ->setData([
                    'token' => $token,
                    'order_found' => $order ? true : false,
                    'order_id' => $order ? $order->id : null,
                    'order_amount' => $order ? $order->amount : null,
                    'stripe_key_configured' => !empty(env('STRIPE_SECRET_KEY')),
                    'payment_setting' => get_payment_setting('secret', 'stripe'),
                ])
                ->setMessage('Test successful!');
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage('Test failed: ' . $exception->getMessage());
        }
    }
}
