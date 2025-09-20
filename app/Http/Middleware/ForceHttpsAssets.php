<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsAssets
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Only process HTML responses
        if ($response->headers->get('Content-Type') && 
            str_contains($response->headers->get('Content-Type'), 'text/html')) {
            
            $content = $response->getContent();
            
            // Force HTTPS for all asset URLs in the content
            $content = $this->forceHttpsForAssets($content, $request);
            
            $response->setContent($content);
        }
        
        return $response;
    }
    
    /**
     * Force HTTPS for all asset URLs
     */
    private function forceHttpsForAssets(string $content, Request $request): string
    {
        $currentHost = $request->getHost();
        $currentScheme = $request->getScheme();
        
        // Only apply to ngrok domains or when HTTPS is detected
        if (!str_contains($currentHost, 'ngrok-free.app') && 
            !str_contains($currentHost, 'ngrok.io') && 
            $currentScheme !== 'https') {
            return $content;
        }
        
        // Fix asset URLs
        $patterns = [
            // CSS files
            '/href=["\'](http:\/\/' . preg_quote($currentHost, '/') . '[^"\']*\.css[^"\']*)["\']/' => 'href="$1"'.str_replace('http://', 'https://', '$1'),
            
            // JS files  
            '/src=["\'](http:\/\/' . preg_quote($currentHost, '/') . '[^"\']*\.js[^"\']*)["\']/' => 'src="$1"'.str_replace('http://', 'https://', '$1'),
            
            // Font files
            '/src=["\'](http:\/\/' . preg_quote($currentHost, '/') . '[^"\']*\.(woff|woff2|ttf|eot)[^"\']*)["\']/' => 'src="$1"'.str_replace('http://', 'https://', '$1'),
            
            // Images
            '/src=["\'](http:\/\/' . preg_quote($currentHost, '/') . '[^"\']*\.(jpg|jpeg|png|gif|svg)[^"\']*)["\']/' => 'src="$1"'.str_replace('http://', 'https://', '$1'),
            
            // Background images in style attributes
            '/style=["\'][^"\']*url\(["\']?http:\/\/' . preg_quote($currentHost, '/') . '[^"\']*["\']?\)/' => function($matches) {
                return str_replace('http://', 'https://', $matches[0]);
            },
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            if (is_callable($replacement)) {
                $content = preg_replace_callback($pattern, $replacement, $content);
            } else {
                $content = preg_replace($pattern, $replacement, $content);
            }
        }
        
        return $content;
    }
}
