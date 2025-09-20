<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DebugAirwallexController extends Controller
{
    public function testAuth()
    {
        $baseUrl = env('AIRWALLEX_ENV') === 'production'
            ? 'https://api.airwallex.com'
            : 'https://api-demo.airwallex.com';
        
        $clientId = env('AIRWALLEX_CLIENT_ID');
        $apiKey = env('AIRWALLEX_API_KEY');
        
        // Test 1: Basic auth method
        $basicAuthResponse = Http::withBasicAuth($clientId, $apiKey)
            ->post("$baseUrl/api/v1/authentication/login");
        
        // Test 2: Headers method  
        $headerAuthResponse = Http::withHeaders([
            'x-client-id' => $clientId,
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post("$baseUrl/api/v1/authentication/login");
        
        // Test 3: Body method
        $bodyAuthResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("$baseUrl/api/v1/authentication/login", [
            'x-client-id' => $clientId,
            'x-api-key' => $apiKey,
        ]);

        return response()->json([
            'config' => [
                'base_url' => $baseUrl,
                'client_id' => $clientId,
                'api_key_length' => strlen($apiKey),
                'env' => env('AIRWALLEX_ENV')
            ],
            'basic_auth' => [
                'status' => $basicAuthResponse->status(),
                'response' => $basicAuthResponse->json(),
                'body' => $basicAuthResponse->body()
            ],
            'header_auth' => [
                'status' => $headerAuthResponse->status(),
                'response' => $headerAuthResponse->json(),
                'body' => $headerAuthResponse->body()
            ],
            'body_auth' => [
                'status' => $bodyAuthResponse->status(),
                'response' => $bodyAuthResponse->json(),
                'body' => $bodyAuthResponse->body()
            ]
        ]);
    }

    public function testPayment($token)
    {
        $baseUrl = env('AIRWALLEX_ENV') === 'production'
            ? 'https://api.airwallex.com'
            : 'https://api-demo.airwallex.com';
        
        $clientId = env('AIRWALLEX_CLIENT_ID');
        $apiKey = env('AIRWALLEX_API_KEY');
        
        // Step 1: Get token
        $authResponse = Http::withBasicAuth($clientId, $apiKey)
            ->post("$baseUrl/api/v1/authentication/login");
        
        if (!$authResponse->successful()) {
            return response()->json([
                'error' => 'Auth failed',
                'auth_response' => [
                    'status' => $authResponse->status(),
                    'body' => $authResponse->body(),
                    'json' => $authResponse->json()
                ]
            ]);
        }
        
        $accessToken = $authResponse->json('token');
        
        // Step 2: Create simple payment intent
        $intentData = [
            'request_id' => 'debug_' . time(),
            'amount' => '10.00',
            'currency' => 'USD',
            'merchant_order_id' => $token,
        ];
        
        $intentResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post("$baseUrl/api/v1/pa/payment_intents/create", $intentData);
        
        return response()->json([
            'auth_success' => true,
            'token_length' => strlen($accessToken),
            'intent_request' => $intentData,
            'intent_response' => [
                'status' => $intentResponse->status(),
                'body' => $intentResponse->body(),
                'json' => $intentResponse->json()
            ]
        ]);
    }
}