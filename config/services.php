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

    'google' => [
        'calendar_id' => env('GOOGLE_CALENDAR_ID', 'primary'),
    ],

    'zoom' => [
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
    ],

    'admin_notification_email' => env('ADMIN_NOTIFICATION_EMAIL', 'kingsleykhord@gmail.com'),

    // When enabled, guest bookings skip the real Stripe/Paystack checkout and are
    // marked as paid immediately, so the email + confirmation flow can be tested
    // end-to-end without real money. Must never be true in production.
    'fake_guest_payments' => env('FAKE_GUEST_PAYMENTS', false),

];
