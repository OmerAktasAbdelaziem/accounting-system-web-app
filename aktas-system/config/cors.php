<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure CORS settings for your application. This
    | configuration will be used by the CORS middleware to handle cross-origin
    | requests from JavaScript applications.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('APP_URL', 'http://localhost:8000'),
        'http://127.0.0.1:8000',
        'http://localhost:3000',
        'http://localhost:5173',
        // Add production domains here
        // 'https://yourdomain.com',
    ],

    'allowed_origins_patterns' => [
        // Allow all localhost variants in development
        '/localhost:*/i',
        '/127\.0\.0\.1:.*/i',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'Content-Length',
        'X-JSON-Response',
        'X-Total-Count',
        'X-Page-Count',
        'X-Per-Page',
        'X-From',
        'X-To',
    ],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => true,
];
