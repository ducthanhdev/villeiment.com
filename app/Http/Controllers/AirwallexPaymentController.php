<?php

namespace App\Http\Controllers;

use Botble\Ecommerce\Models\Order;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AirwallexPaymentController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $env = env('AIRWALLEX_ENV', 'demo');
        $this->baseUrl = $env === 'prod' ? 'https://api.airwallex.com' : 'https://api-demo.airwallex.com';
    }

    private function getAccessToken()
    {
        $clientId = env('AIRWALLEX_CLIENT_ID');
        $apiKey = env('AIRWALLEX_API_KEY');
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-client-id' => $clientId,
            'x-api-key' => $apiKey,
        ])->post($this->baseUrl . '/api/v1/authentication/login');

        if (!$response->successful()) {
            throw new \Exception('Authentication failed: ' . $response->body());
        }

        return $response->json('token');
    }

    public function createIntent(Request $request, $token)
    {
        try {
            $order = Order::where('token', $token)->firstOrFail();
            $accessToken = $this->getAccessToken();
            
            $intentData = [
                'request_id' => uniqid('req_'),
                'amount' => $order->amount,
                'currency' => strtoupper(get_application_currency()->title ?? 'USD'),
                'merchant_order_id' => $order->code,
                'descriptor' => 'Order #' . $order->code,
                'metadata' => [
                    'order_id' => $order->id,
                    'integration' => 'embedded_elements'
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/v1/pa/payment_intents/create', $intentData);

            if (!$response->successful()) {
                throw new \Exception('Payment intent creation failed: ' . $response->body());
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function startSession(Request $request, $token)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            $body = [
                'payment_intent_id' => $request->payment_intent_id,
                'validation_url' => $request->validation_url,
                'initiative_context' => $request->initiative_context
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/v1/pa/payment_session/start', $body);

            if (!$response->successful()) {
                throw new \Exception('Session start failed: ' . $response->body());
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 400);
        }
    }

    private function getPaymentIntent($paymentIntentId, $accessToken)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get($this->baseUrl . '/api/v1/pa/payment_intents/' . $paymentIntentId);

        if (!$response->successful()) {
            throw new \Exception('Failed to retrieve payment intent: ' . $response->body());
        }

        return $response->json();
    }

    public function confirmPayment(Request $request, $token)
    {
        try {
            $order = Order::where('token', $token)->firstOrFail();
            $paymentIntentId = $request->input('payment_intent_id');
            $paymentData = $request->input('payment_data');
            $method = $request->input('method', 'card');

            if (!$paymentIntentId) {
                throw new \Exception('Payment intent ID is required');
            }

            $accessToken = $this->getAccessToken();

            if ($method === 'googlePay' || $method === 'applePay') {
                $body = [];

                if ($method === 'googlePay') {
                    $token = $paymentData['paymentMethodData']['tokenizationData']['token'];
                    $body = [
                        'payment_data_type' => 'encrypted_payment_token',
                        'encrypted_payment_token' => $token
                    ];
                } elseif ($method === 'applePay') {
                    $paymentToken = $paymentData;
                    $body = [
                        'payment_data_type' => 'tokenized_card', // Adjust if needed
                        'payment_method' => [
                            'applepay' => [
                                'card_brand' => $paymentToken['paymentMethod']['network'],
                                'card_type' => strtolower($paymentToken['paymentMethod']['type']),
                                'data' => $paymentToken['paymentData']['data'],
                                'ephemeral_public_key' => $paymentToken['paymentData']['header']['ephemeralPublicKey'],
                                'public_key_hash' => $paymentToken['paymentData']['header']['publicKeyHash'],
                                'transaction_id' => $paymentToken['paymentData']['header']['transactionId'],
                                'signature' => $paymentToken['paymentData']['signature'],
                                'version' => $paymentToken['paymentData']['version']
                            ]
                        ]
                    ];
                }

                $confirmResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post($this->baseUrl . '/api/v1/pa/payment_intents/' . $paymentIntentId . '/confirm', $body);

                if (!$confirmResponse->successful()) {
                    throw new \Exception('Payment confirmation failed: ' . $confirmResponse->body());
                }

                $intent = $confirmResponse->json();
            } else {
                // For card, verify status
                $intent = $this->getPaymentIntent($paymentIntentId, $accessToken);
            }

            if ($intent['status'] !== 'SUCCEEDED') {
                throw new \Exception('Payment not succeeded. Status: ' . $intent['status']);
            }

            // Create payment record
            $payment = Payment::create([
                'amount' => $order->amount,
                'currency' => strtoupper(get_application_currency()->title ?? 'USD'),
                'charge_id' => $paymentIntentId,
                'payment_channel' => 'airwallex',
                'status' => PaymentStatusEnum::COMPLETED,
                'order_id' => $order->id,
                'customer_id' => $order->user_id,
                'customer_type' => $order->user ? get_class($order->user) : null,
                'description' => ucfirst($method) // To display the selected payment method
            ]);

            // Update order status
            $order->update([
                'status' => \Botble\Ecommerce\Enums\OrderStatusEnum::PROCESSING,
                'payment_id' => $payment->id,
            ]);

            return response()->json([
                'success' => true,
                'redirect_url' => route('public.checkout.success', $token)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}