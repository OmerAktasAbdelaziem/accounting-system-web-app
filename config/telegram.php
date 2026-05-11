<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Telegram bot notifications
    |
    */

    'enabled' => env('TELEGRAM_ENABLED', true),
    'token' => env('TELEGRAM_BOT_TOKEN', '8349908920:AAEGIy2sUdFRfEnx4ZnXRcXtCSFxdgbIdAM'),
    'chat_id' => env('TELEGRAM_CHAT_ID', '5173560887'),
    'username' => env('TELEGRAM_USERNAME', '@hanody_systembot'),

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure which events trigger Telegram notifications
    |
    */

    'notifications' => [
        'errors' => env('TELEGRAM_NOTIFY_ERRORS', true),
        'exceptions' => env('TELEGRAM_NOTIFY_EXCEPTIONS', true),
        'submissions' => env('TELEGRAM_NOTIFY_SUBMISSIONS', true),
        'auth' => env('TELEGRAM_NOTIFY_AUTH', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Endpoint
    |--------------------------------------------------------------------------
    |
    | Telegram Bot API endpoint
    |
    */

    'api_url' => 'https://api.telegram.org',
];
