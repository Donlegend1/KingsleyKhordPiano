<?php

$isLive = env('PAYMENT_MODE', 'test') === 'live';

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

'paystack' => [
    'secret_key' => $isLive
        ? env('PAYSTACK_LIVE_SECRET_KEY')
        : env('PAYSTACK_TEST_SECRET_KEY'),
],

'stripe' => [
    'key' => $isLive
        ? env('STRIPE_LIVE_KEY')
        : env('STRIPE_TEST_KEY'),

    'secret' => $isLive
        ? env('STRIPE_LIVE_SECRET')
        : env('STRIPE_TEST_SECRET'),
    ' webhook_secret ' => env('STRIPE__WEBHOOK_SECRET'),  
],

    'paypal' => [
        'client_id' => $isLive
            ? env('PAYPAL_LIVE_CLIENT_ID')
            : env('PAYPAL_TEST_CLIENT_ID'),
        'secret' => $isLive
            ? env('PAYPAL_LIVE_CLIENT_SECRET')
            : env('PAYPAL_TEST_CLIENT_SECRET'),
        'currency' => env('PAYPAL_CURRENCY', 'USD'),
        'test_mode' => env('PAYPAL_MODE', 'sandbox')
    ],

];
