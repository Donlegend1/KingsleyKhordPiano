<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe Secret Key
    |--------------------------------------------------------------------------
    |
    | This is your Stripe Secret Key, which is used to authenticate requests
    | to Stripe's API.
    |
    */

    'secret_key' => env('PAYMENT_MODE') === 'live' ? env('STRIPE_SECRET') : env('STRIPE_TEST_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Public Key
    |--------------------------------------------------------------------------
    |
    | This is your Stripe Public Key, which is used to initialize payments
    | in the client-side.
    |
    */

    'public_key' => env('STRIPE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Payment URL
    |--------------------------------------------------------------------------
    |
    | This is the Stripe API endpoint for payment-related functionalities.
    |
    */

    'payment_url' => 'https://api.stripe.com',

    /*
    |--------------------------------------------------------------------------
    | Stripe Merchant Email
    |--------------------------------------------------------------------------
    |
    | This is the email address you use to register on Stripe.
    |
    */

    'merchant_email' => env('MERCHANT_EMAIL'),
    'callback_url'=>env('STRIPE_CALLBACK'),
    'cancel_url'=>env('STRIPE_CALLBACK')

];