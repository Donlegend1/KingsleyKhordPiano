<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaystackService
{
    /**
     * Initialize Paystack Checkout Session.
     */
    public function initialize($user, Plan $plan, $currency)
    {
        $reference = Str::uuid()->toString();

        Subscription::create([
            'user_id' => $user->id,
            'type' => 'default',
            'stripe_status' => 'pending',
            'payment_method' => 'paystack',
        ]);

        $payload = [
            'email' => $user->email,
            'plan' => $plan->paystack_product_id,
            'reference' => $reference,
            "amount"=> $plan->price_ngn * 100, 
            'callback_url' => route('payment.verify'),
            'metadata' => [
                'user_id' => $user->id,
            ],
        ];

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->acceptJson()
            ->post('https://api.paystack.co/transaction/initialize', $payload);

        return [
            'response' => $response,
            'payload' => $payload,
        ];
    }

    /**
     * Verify Paystack Transaction.
     */
    public function verify($reference)
    {
        return Http::withToken(config('services.paystack.secret_key'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");
    }
}
