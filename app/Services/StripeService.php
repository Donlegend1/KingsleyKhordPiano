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
    public function createPaymentIntent($user, $request)
    {
        $reference = \Illuminate\Support\Str::uuid()->toString();
        $plan = \App\Models\Plan::where('type', $request->duration)->where('tier', $request->tier)->first();
        $currency = strtoupper($request->currency);
        $amount = match ($currency) {
            'USD' => $plan?->price_usd,
            'EUR' => $plan?->price_eur,
            default => null,
        };
        if (!$plan || !$amount) {
            throw new \Exception('Plan or currency not found.');
        }
        DB::table('payments')->insert([
            'user_id'       => $user->id,
            'reference'     => $reference,
            'amount'        => $amount,
            'metadata'      => json_encode($request->only(['currency', 'tier', 'duration'])),
            'payment_method'=> 'stripe',
            'notified_at'   => null,
            'starts_at'     => now(),
            'ends_at'       => $request->duration === "monthly" ? now()->addMonth() : now()->addYear(),
            'status'        => 'pending',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        $user->metadata = $request->only(['currency', 'tier', 'duration']);
        $user->payment_method = 'stripe';
        $user->premium = $request->tier === 'premium';
        $user->last_payment_reference = $reference;
        $user->last_payment_amount = $amount;
        $user->last_payment_at = now();
        $user->save();
        try {
            $session = $this->stripe->checkout->sessions->create([
                'line_items' => [[
                    'price_data' => [
                        'currency'     => $request->currency,
                        'product_data' => [
                            'name'        => $request->tier,
                            'description' => 'Subscription for ' . config('app.name'),
                        ],
                        'unit_amount'  => $amount * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url('/stripe/success') . '?session_id={CHECKOUT_SESSION_ID}&reference=' . $reference,
                'cancel_url' => url('/stripe/cancel'),
                'metadata' => [
                    'user_id' => $user->id,
                    'reference' => $reference,
                    'tier' => $request->tier,
                    'duration' => $request->duration,
                ],
            ]);
            return $session->url;
        } catch (\Exception $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());
            throw new \Exception('Stripe error: ' . $e->getMessage());
        }
    }

    /**
     * Verify Payment and Update Status
     */
    public function retrievePaymentIntent($sessionId, $reference)
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($sessionId);
            if ($session->status === 'complete' || $session->payment_status === 'paid') {
                DB::table('payments')
                    ->where('reference', $reference)
                    ->update([
                        'status' => 'successful',
                        'updated_at' => now(),
                    ]);
                $user = \Illuminate\Support\Facades\Auth::user();
                $user->payment_status = 'successful';
                $user->save();
                return ['success' => true];
            } else {
                DB::table('payments')
                    ->where('reference', $reference)
                    ->update([
                        'status' => 'failed',
                        'updated_at' => now(),
                    ]);
                return ['success' => false];
            }
        } catch (\Exception $e) {
            Log::error('Stripe Payment Error: ' . $e->getMessage());
            throw new \Exception('Stripe error: ' . $e->getMessage());
        }
    }
    public function handleStripeWebHook($payload, $sig_header, $endpoint_secret)
    {
        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $userId   = $session->metadata->user_id ?? null;
                $tier     = $session->metadata->tier ?? null;
                $duration = $session->metadata->duration ?? 'monthly';
                $currency = $session->currency ?? 'eur';
                $amount   = $session->amount_total / 100;
                if (!$userId || !$tier) {
                    Log::error("Missing metadata in Stripe session: " . json_encode($session->metadata));
                    return response('Missing metadata', 400);
                }
                DB::table('payments')->updateOrInsert(
                    ['reference' => $session->id],
                    [
                        'user_id'        => $userId,
                        'reference'      => $session->id,
                        'amount'         => $amount,
                        'metadata'       => json_encode([
                            'currency' => $currency,
                            'tier'     => $tier,
                            'duration' => $duration,
                        ]),
                        'payment_method' => 'stripe',
                        'notified_at'    => null,
                        'starts_at'      => now(),
                        'ends_at'        => $duration === "monthly" ? now()->addMonth() : now()->addYear(),
                        'status'         => 'successful',
                        'updated_at'     => now(),
                    ]
                );
                break;
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                DB::table('payments')
                    ->where('reference', $invoice->id)
                    ->update(['status' => 'successful', 'updated_at' => now()]);
                break;
            default:
                Log::info("Unhandled Stripe event: {$event->type}");
                break;
        }
        return response('Webhook received', 200);
    }
}
