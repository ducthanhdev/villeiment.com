<?php
use App\Http\Controllers\AirwallexPaymentController;
use App\Http\Controllers\DebugAirwallexController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Stripe routes - put at the top to avoid conflicts

Route::post('checkout/airwallex/create-intent/{token}', [AirwallexPaymentController::class, 'createIntent'])
    ->name('public.checkout.airwallex.createIntent');

Route::post('checkout/airwallex/start-session/{token}', [AirwallexPaymentController::class, 'startSession'])
    ->name('public.checkout.airwallex.startSession');

Route::post('checkout/airwallex/confirm-payment/{token}', [AirwallexPaymentController::class, 'confirmPayment'])
    ->name('public.checkout.airwallex.confirmPayment');

// Stripe routes defined below as closures



// Stripe Payment Intent creation
Route::post('stripe/create-intent/{token}', function ($token) {
    try {
        $order = \Botble\Ecommerce\Models\Order::where('token', $token)->first();
        
        if (!$order) {
            return response()->json([
                'error' => true,
                'message' => 'Order not found!'
            ]);
        }

        $stripeSecretKey = env('STRIPE_SECRET_KEY');
        if (!$stripeSecretKey) {
            return response()->json([
                'error' => true,
                'message' => 'Stripe secret key not configured!'
            ]);
        }

        \Stripe\Stripe::setApiKey($stripeSecretKey);

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => (int) ($order->amount * 100), // Convert to cents
            'currency' => strtolower($order->currency_id),
            'metadata' => [
                'order_id' => $order->id,
                'token' => $token,
            ],
        ]);

        return response()->json([
            'success' => true,
            'client_secret' => $paymentIntent->client_secret,
            'payment_intent_id' => $paymentIntent->id,
            'message' => 'Payment intent created successfully!'
        ]);

    } catch (\Exception $exception) {
        return response()->json([
            'error' => true,
            'message' => $exception->getMessage()
        ]);
    }
})->withoutMiddleware(['web']);

// Stripe Payment Confirmation
Route::post('stripe/confirm/{token}', function ($token, Request $request) {
    try {
        $order = \Botble\Ecommerce\Models\Order::where('token', $token)->first();
        
        if (!$order) {
            return response()->json([
                'error' => true,
                'message' => 'Order not found!'
            ]);
        }

        $stripeSecretKey = env('STRIPE_SECRET_KEY');
        if (!$stripeSecretKey) {
            return response()->json([
                'error' => true,
                'message' => 'Stripe secret key not configured!'
            ]);
        }

        \Stripe\Stripe::setApiKey($stripeSecretKey);

        $paymentIntentId = $request->input('payment_intent_id');
        $paymentMethodId = $request->input('payment_method_id');
        $method = $request->input('method', 'google_pay');

        if (!$paymentIntentId || !$paymentMethodId) {
            return response()->json([
                'error' => true,
                'message' => 'Missing payment_intent_id or payment_method_id!'
            ]);
        }

        // Confirm the payment intent
        $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
        $paymentIntent->confirm([
            'payment_method' => $paymentMethodId,
        ]);

        // Update order status
        $order->update([
            'payment_status' => 'completed',
            'status' => 'processing'
        ]);

        // Create payment record
        \Botble\Payment\Models\Payment::create([
            'charge_id' => $paymentIntent->id,
            'amount' => $order->amount,
            'currency' => $order->currency_id,
            'status' => 'completed',
            'payment_channel' => 'stripe',
            'order_id' => $order->id,
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => route('public.checkout.success', $token),
            'message' => 'Payment confirmed successfully!'
        ]);

    } catch (\Exception $exception) {
        return response()->json([
            'error' => true,
            'message' => $exception->getMessage()
        ]);
    }
})->withoutMiddleware(['web']);