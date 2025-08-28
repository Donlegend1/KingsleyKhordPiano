<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Plan;

class StripeController extends Controller
{
    protected $stripeService;
    protected $stripeKey;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        $this->stripeKey = config('services.stripe.secret');
    }

    /**
     * Create Stripe Checkout Session
     */
    public function createPaymentIntent(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'currency' => 'required|string',
            'tier' => 'required|string',
            'duration' => 'required|in:monthly,yearly',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect('register');
        }

        $reference = Str::uuid()->toString();

         $plan = Plan::where('type', $request->duration)->where('tier', $request->tier)->first();
       
        // Store payment record
        DB::table('payments')->insert([
            'user_id'       => $user->id,
            'reference'     => $reference,
            'amount'        => $plan->price_eur,
            'metadata'      => json_encode($request->only(['currency', 'tier', 'duration'])),
            'payment_method'=> 'stripe',
            'notified_at'   => null,
            'starts_at'     => now(),
            'ends_at'       => $request->duration === "monthly" ? now()->addMonths() : now()->addYear(),
            'status'        => 'pending',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Update user payment info
        $user->metadata = $request->only(['currency', 'tier', 'duration']);
        $user->payment_method = 'stripe';
        $user->premium = $request->tier === 'premium';
        $user->last_payment_reference = $reference;
        $user->last_payment_amount = $plan->price_eur;
        $user->last_payment_at = now();
        $user->save();

        // Create Stripe Checkout Session
        $stripe = new \Stripe\StripeClient($this->stripeKey);

        $checkoutSession = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency'     => $request->currency,
                    'product_data' => [
                    'name'        => $request->tier,
                    'description' => 'Subscription for ' . config('app.name'), 
                ],
                    'unit_amount'  => $plan->price_eur * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/stripe/success') . '?session_id={CHECKOUT_SESSION_ID}&reference=' . $reference,
            'cancel_url' => url('/stripe/cancel'),
        ]);

        // Redirect to Stripe
        return redirect()->away($checkoutSession->url);
    }

    /**
     * Handle Stripe Checkout Redirect
     */
    public function retrievePaymentIntent(Request $request)
    {
        try {
            $stripe = new \Stripe\StripeClient($this->stripeKey);

            $session = $stripe->checkout->sessions->retrieve($request->input('session_id'));

            if ($session->status === 'complete') {
                DB::table('payments')
                    ->where('reference', $request->reference)
                    ->update([
                        'status' => 'successful',
                        'updated_at' => now(),
                    ]);

                $user = Auth::user();
                $user->payment_status = 'successful';
                $user->save();

                return redirect()->route('home')->with('success', 'Payment Successful!');
            } else {
                DB::table('payments')
                    ->where('reference', $request->reference)
                    ->update([
                        'status' => 'failed',
                        'updated_at' => now(),
                    ]);

                return redirect()->route('home')->with('error', 'Payment not verified.');
            }
        } catch (\Exception $e) {
            Log::error('Stripe Payment Error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'An error occurred while processing your payment.');
        }
    }

    public function stripeWebHook(Request $request)
    {
        $stripe = new \Stripe\StripeClient($this->stripeKey);
        $endpoint_secret = config('services.stripe.webhook_secret'); 
        $payload = $request->getContent();
        $sig_header = $request->server('HTTP_STRIPE_SIGNATURE');
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
                    \Log::error("Missing metadata in Stripe session: " . json_encode($session->metadata));
                    return response('Missing metadata', 400);
                }

                $user = User::find($userId);
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
                \Log::info("Unhandled Stripe event: {$event->type}");
                break;
        }

        return response('Webhook received', 200);
    }
}
