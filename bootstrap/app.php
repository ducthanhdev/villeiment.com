<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            require __DIR__.'/../routes/api-stripe.php';
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Force HTTPS for assets on ngrok domains
        $middleware->web(append: [
            \App\Http\Middleware\ForceHttpsAssets::class,
        ]);
    })
    ->withProviders([
        \App\Providers\AssetServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
