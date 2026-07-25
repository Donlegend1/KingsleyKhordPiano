<?php

namespace App\Services;

use Omnipay\Omnipay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Plan;
use App\Models\Subscription;

class PayPalService
{
    private $gateway;

    public function __construct()
    {
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId(config('services.paypal.client_id'));
        $this->gateway->setSecret(config('services.paypal.secret'));
        $this->gateway->setTestMode(config('services.paypal.test_mode'));
    }

    /**
     * Start the PayPal purchase process.
     */
    public function purchase($user, Plan $plan, $request)
    {
        $reference = Str::uuid()->toString();

        // Create or update subscription record as pending
        Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'stripe_id' => 'paypal_pending_' . $reference,
                'stripe_status' => 'pending',
                'ends_at' => $request->duration === "monthly"
                    ? now()->addMonth(1)
                    : ($request->duration === "quarterly" ? now()->addMonths(3) : now()->addYear()),
                'type' => 'default',
                'payment_method' => 'paypal',
            ]
        );

        $user->metadata = $request->all();
        $user->payment_method = 'paypal';
        $user->premium = $request->tier === 'premium';
        $user->last_payment_reference = $reference;
        $user->last_payment_amount = $plan->price_usd;
        $user->last_payment_at = now();
        $user->save();

        $response = $this->gateway->purchase([
            'amount' => $plan->price_usd,
            'currency' => $request->currency,
            'returnUrl' => url('paypal/success'),
            'cancelUrl' => url('paypal/cancel'),
        ])->send();

        return $response;
    }

    /**
     * Complete the PayPal transaction.
     */
    public function completePurchase($paymentId, $payerId)
    {
        $transaction = $this->gateway->completePurchase([
            'payer_id' => $payerId,
            'transactionReference' => $paymentId
        ])->send();

        if ($transaction->isSuccessful()) {
            $arr = $transaction->getData();
            $user = Auth::user();

            if (!$user) {
                throw new \Exception('User session expired.');
            }

            // Find the pending subscription
            $subscription = Subscription::where('user_id', $user->id)
                ->where('payment_method', 'paypal')
                ->latest()
                ->first();

            $endsAt = $subscription ? $subscription->ends_at : now()->addMonth();

            if ($subscription) {
                $subscription->update([
                    'stripe_id' => 'paypal_' . $arr['id'],
                    'stripe_status' => 'active',
                ]);
            } else {
                Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => 'default',
                        'stripe_id' => 'paypal_' . $arr['id'],
                        'stripe_status' => 'active',
                        'ends_at' => $endsAt,
                        'type' => 'default',
                        'payment_method' => 'paypal',
                    ]
                );
            }

            // Update user premium status
            $tier = $user->metadata['tier'] ?? 'standard';

            $user->update([
                'premium' => $tier === 'premium',
                'payment_method' => 'paypal',
                'last_payment_reference' => $arr['id'],
                'last_payment_amount' => $arr['transactions'][0]['amount']['total'] ?? 0,
                'payment_status' => 'successful',
                'last_payment_at' => now(),
            ]);

            return true;
        }

        return false;
    }
}
