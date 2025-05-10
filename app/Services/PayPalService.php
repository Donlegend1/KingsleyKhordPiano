<?php

namespace App\Services;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new PayPalClient();
        $this->provider->setApiCredentials(config('paypal'));
    }

    public function createOrder($amount, $currency = 'USD')
    {
        try {
            $order = $this->provider->createOrder([
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => $currency,
                            "value" => $amount
                        ]
                    ]
                ]
            ]);

            return $order;
        } catch (\Exception $e) {
            Log::error('PayPal Order Creation Error: ' . $e->getMessage());
            return null;
        }
    }

    public function captureOrder($orderId)
    {
        try {
            $response = $this->provider->capturePaymentOrder($orderId);
            return $response;
        } catch (\Exception $e) {
            Log::error('PayPal Order Capture Error: ' . $e->getMessage());
            return null;
        }
    }
}
