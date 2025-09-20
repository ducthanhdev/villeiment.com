<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AssetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Force HTTPS for ngrok domains
        if (request() && (
            str_contains(request()->getHost(), 'ngrok-free.app') || 
            str_contains(request()->getHost(), 'ngrok.io') ||
            request()->header('x-forwarded-proto') === 'https'
        )) {
            URL::forceScheme('https');
        }
        
        // Override asset helper globally
        $this->overrideAssetHelper();
    }
    
    /**
     * Override Laravel's asset helper to force HTTPS
     */
    private function overrideAssetHelper(): void
    {
        // Create a custom asset helper that forces HTTPS
        if (!function_exists('secure_asset_global')) {
            function secure_asset_global($path = null, $secure = null)
            {
                $url = url($path, [], $secure);
                
                // Force HTTPS for ngrok domains
                if (request() && (
                    str_contains(request()->getHost(), 'ngrok-free.app') || 
                    str_contains(request()->getHost(), 'ngrok.io') ||
                    request()->header('x-forwarded-proto') === 'https'
                )) {
                    $url = str_replace('http://', 'https://', $url);
                }
                
                return $url;
            }
        }
    }
}
