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
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'alphavantage' => [
        'key' => env('ALPHAVANTAGE_API_KEY'),
    ],

    'polygon' => [
        'key' => env('POLYGON_API_KEY'),
    ],

    'finnhub' => [
        'key' => env('FINNHUB_API_KEY'),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY', env('GOOGLE_API_KEY')),
    ],

    'groq' => [
        'key' => env('GROQ_API_KEY'),
    ],

    'openrouter' => [
        'key' => env('OPENROUTER_API_KEY'),
    ],

    'copilot' => [
        'key' => env('COPILOT_API_KEY', env('GITHUB_TOKEN')),
    ],

    'nvidia' => [
        'key' => env('NVIDIA_API_KEY'),
    ],

    'cerebras' => [
        'key' => env('CEREBRAS_API_KEY'),
    ],

];
