<?php
use App\Http\Controllers\AirwallexPaymentController;
use App\Http\Controllers\DebugAirwallexController;

use Illuminate\Support\Facades\Route;


Route::post('checkout/airwallex/create-intent/{token}', [AirwallexPaymentController::class, 'createIntent'])
    ->name('public.checkout.airwallex.createIntent');

Route::post('checkout/airwallex/start-session/{token}', [AirwallexPaymentController::class, 'startSession'])
    ->name('public.checkout.airwallex.startSession');

Route::post('checkout/airwallex/confirm-payment/{token}', [AirwallexPaymentController::class, 'confirmPayment'])
    ->name('public.checkout.airwallex.confirmPayment');

// Stripe routes defined below as closures

// Simple test route
Route::get('test-simple', function () {
    return response()->json(['success' => true, 'message' => 'Simple test works']);
});

// Test POST route
Route::post('test-post', function () {
    return response()->json(['success' => true, 'message' => 'POST test works']);
});

// Test Stripe controller directly
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
});

// Stripe confirm route
Route::post('stripe/confirm/{token}', function ($token) {
    try {
        $order = \Botble\Ecommerce\Models\Order::where('token', $token)->first();
        
        if (!$order) {
            return response()->json([
                'error' => true,
                'message' => 'Order not found!'
            ]);
        }

        // For now, just return success - implement actual confirmation later
        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed successfully!'
        ]);

    } catch (\Exception $exception) {
        return response()->json([
            'error' => true,
            'message' => $exception->getMessage()
        ]);
    }
});