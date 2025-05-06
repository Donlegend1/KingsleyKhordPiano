<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paystack Secret Key
    |--------------------------------------------------------------------------
    |
    | This is your Paystack Secret Key, which is used to authenticate requests
    | to Paystack's API.
    |
    */

    'secret_key' => env('PAYSTACK_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Paystack Public Key
    |--------------------------------------------------------------------------
    |
    | This is your Paystack Public Key, which is used to initialize payments
    | in the client-side.
    |
    */

    'public_key' => env('PAYSTACK_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Paystack Payment URL
    |--------------------------------------------------------------------------
    |
    | This is the Paystack API endpoint for payment-related functionalities.
    |
    */

    'payment_url' => 'https://api.paystack.co',

    /*
    |--------------------------------------------------------------------------
    | Paystack Merchant Email
    |--------------------------------------------------------------------------
    |
    | This is the email address you use to register on Paystack.
    |
    */

    'merchant_email' => env('MERCHANT_EMAIL'),
    'callback_url'=>env('PAYSTACK_CALLBACK'),
    'cancel_url'=>env('PAYSTACK_CALLBACK')

];
