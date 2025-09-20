<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force HTTPS for ngrok domains
        if ($request->header('x-forwarded-proto') !== 'https' && 
            (str_contains($request->getHost(), 'ngrok-free.app') || 
             str_contains($request->getHost(), 'ngrok.io'))) {
            
            return redirect()->secure($request->getRequestUri());
        }
        
        return $next($request);
    }
}
