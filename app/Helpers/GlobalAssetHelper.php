<?php

namespace App\Helpers;

class GlobalAssetHelper
{
    /**
     * Override Laravel's asset() helper to force HTTPS for ngrok domains
     */
    public static function init()
    {
        // Override the global asset() function
        if (!function_exists('secure_asset_override')) {
            function secure_asset_override($path = null, $secure = null)
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
