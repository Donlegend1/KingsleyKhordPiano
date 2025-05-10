<?php

namespace App\Services;

use Stripe\StripeClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StripeService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a Checkout Session and Save Transaction
     */
    public function createCheckoutSession($subscription, $user)
    {
        $reference = uniqid('stripe_', true);

        // Save transaction to the database
        $transaction = DB::table('payments')->insertGetId([
            'user_id' => $user->id,
            'reference' => $reference,
            'amount' => $subscription->amount_dollar * 100,
            'payment_method' => 'stripe',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $session = $this->stripe->checkout->sessions->create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $subscription->name,
                        ],
                        'unit_amount' => $subscription->amount_dollar * 100,
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'transaction_id' => $transaction,
                    'reference' => $reference,
                ],
                'mode' => 'payment',
                'success_url' => route('stripe.verify') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.failed'),
            ]);

            return $session->url;

        } catch (\Exception $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify Payment and Update Status
     */
    public function verifyPayment($sessionId)
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $transactionId = $session->metadata->transaction_id;

                DB::table('payments')->where('id', $transactionId)->update([
                    'status' => 'completed',
                    'updated_at' => now(),
                ]);

                return true;
            }

        } catch (\Exception $e) {
            Log::error('Stripe Verification Error: ' . $e->getMessage());
        }

        return false;
    }
}
