<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ngrok Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for ngrok HTTPS testing
    |
    */

    'force_https' => env('FORCE_HTTPS', true),
    
    'allowed_hosts' => [
        'localhost',
        '127.0.0.1',
        '*.ngrok-free.app',
        '*.ngrok.io',
    ],
    
    'trust_proxies' => [
        '127.0.0.1',
        'localhost',
        '*.ngrok-free.app',
        '*.ngrok.io',
    ],
];
