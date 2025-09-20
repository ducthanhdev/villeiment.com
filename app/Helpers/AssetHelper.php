<?php

namespace App\Helpers;

class AssetHelper
{
    /**
     * Generate secure asset URL
     */
    public static function secureAsset($path)
    {
        $url = asset($path);
        
        // Force HTTPS for ngrok domains
        if (request()->header('x-forwarded-proto') === 'https' || 
            str_contains(request()->getHost(), 'ngrok-free.app') || 
            str_contains(request()->getHost(), 'ngrok.io')) {
            $url = str_replace('http://', 'https://', $url);
        }
        
        return $url;
    }
    
    /**
     * Generate secure URL
     */
    public static function secureUrl($path = '')
    {
        $url = url($path);
        
        // Force HTTPS for ngrok domains
        if (request()->header('x-forwarded-proto') === 'https' || 
            str_contains(request()->getHost(), 'ngrok-free.app') || 
            str_contains(request()->getHost(), 'ngrok.io')) {
            $url = str_replace('http://', 'https://', $url);
        }
        
        return $url;
    }
}
