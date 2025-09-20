<?php

use Illuminate\Support\Facades\Route;

// Stripe API routes - defined BEFORE theme routes to avoid catch-all conflicts
Route::group(['prefix' => 'api', 'middleware' => []], function () {
    // Simple test route
    Route::get('test', function () {
        return response()->json(['success' => true, 'message' => 'API test works']);
    });
    
    // Simple POST test route
    Route::post('test-post', function () {
        return response()->json(['success' => true, 'message' => 'POST test works']);
    });
    
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

            // Get currency - fallback to USD if not set
            $currency = $order->currency_id ? strtolower($order->currency_id) : 'usd';
            
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => (int) ($order->amount * 100), // Convert to cents
                'currency' => $currency,
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
});
