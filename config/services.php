<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ampi' => [
        'api_key' => env('AMPI_API_KEY'),
        'base_url' => env('AMPI_BASE_URL', 'https://ampisanmigueldeallende.com'),
        'docs_url' => env('AMPI_DOCS_URL', 'https://ampisanmigueldeallende.com/docs?api-docs.json'),
        'http' => [
            'connect_timeout_seconds' => env('AMPI_CONNECT_TIMEOUT_SECONDS', 3),
            'timeout_seconds' => env('AMPI_TIMEOUT_SECONDS', 8),
            'retry_times' => env('AMPI_RETRY_TIMES', 2),
            'retry_sleep_milliseconds' => env('AMPI_RETRY_SLEEP_MILLISECONDS', 200),
        ],
        'cache' => [
            'search_ttl_minutes' => env('AMPI_SEARCH_CACHE_TTL_MINUTES', 5),
            'property_ttl_minutes' => env('AMPI_PROPERTY_CACHE_TTL_MINUTES', 15),
        ],
    ],

];
