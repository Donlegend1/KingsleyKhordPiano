<?php

$mode = env('PAYPAL_MODE', 'sandbox') === 'live' ? 'live' : 'sandbox';

return [
    'mode' => $mode,

    'sandbox' => [
        'client_id' => env('PAYPAL_TEST_CLIENT_ID', env('PAYPAL_SANDBOX_CLIENT_ID', '')),
        'client_secret' => env('PAYPAL_TEST_CLIENT_SECRET', env('PAYPAL_SANDBOX_CLIENT_SECRET', '')),
        'app_id' => env('PAYPAL_SANDBOX_APP_ID', 'APP-80W284485P519543T'),
    ],

    'live' => [
        'client_id' => env('PAYPAL_LIVE_CLIENT_ID', ''),
        // Accept typo spelling still present in some deployed .env files.
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET')
            ?: env('PAYPAL_lIVE_CLIENT_SECRET', ''),
        'app_id' => env('PAYPAL_LIVE_APP_ID', ''),
    ],

    'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'),
    'currency' => env('PAYPAL_CURRENCY', 'USD'),
    'notify_url' => env('PAYPAL_NOTIFY_URL', ''),
    'locale' => env('PAYPAL_LOCALE', 'en_US'),
    'validate_ssl' => (bool) env('PAYPAL_VALIDATE_SSL', true),
];
