<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Plan;
use Laravel\Cashier\Cashier;

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
        $request->validate([
            'currency' => 'required|string',
            'tier' => 'required|string',
            'duration' => 'required|in:monthly,yearly',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect('register');
        }

        try {
            $checkoutUrl = $this->stripeService->createPaymentIntent($user, $request);
            return redirect()->away($checkoutUrl);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Handle Stripe Checkout Redirect
     */
    public function retrievePaymentIntent(Request $request)
    {
        try {
            $result = $this->stripeService->retrievePaymentIntent($request->input('session_id'), $request->reference);
            if ($result['success']) {
                return redirect()->route('home')->with('success', 'Payment Successful!');
            } else {
                return redirect()->route('home')->with('error', 'Payment not verified.');
            }
        } catch (\Exception $e) {
            Log::error('Stripe Payment Error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'An error occurred while processing your payment.');
        }
    }

    public function stripeWebHook(Request $request)
    {
        $endpoint_secret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sig_header = $request->server('HTTP_STRIPE_SIGNATURE');
        return $this->stripeService->handleStripeWebHook($payload, $sig_header, $endpoint_secret);
    }

    public function testStripe(Request $request)
    {
        $request->validate([
            'tier'     => 'required|string',
            'duration' => 'required|in:monthly,yearly',
        ]);

        $user = $request->user();

        $stripePriceId = Plan::where('tier', $request->tier)
            ->where('type', $request->duration)
            ->first()?->price_id;

        if (!$stripePriceId) {
            return response()->json(['error' => 'Invalid plan selected'], 422);
        }

        return $user
            ->newSubscription('default', $stripePriceId)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('checkout-success'),
                'cancel_url'  => route('checkout-cancel'),
                'metadata'    => [ 
                    'user_id'  => $user->id,
                    'tier'     => $request->tier,
                    'duration' => $request->duration,
                ],
            ]);
    }

    public function checkoutSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if ($sessionId === null) {
            return redirect()->route('home')->with('error', 'Invalid session.');
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect()->route('home')->with('error', 'Payment not completed.');
        }

        $userId   = $session->metadata->user_id ?? null;
        $tier     = $session->metadata->tier ?? null;
        $duration = $session->metadata->duration ?? 'monthly';
        $currency = $session->currency ?? 'eur';
        $amount   = ($session->amount_total ?? 0) / 100;

        if (!$userId || !$tier) {
            return redirect()->route('home')->with('error', 'Missing subscription metadata.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('home')->with('error', 'User not found.');
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

        $user->update([
            'last_payment_reference' => $sessionId,
            'payment_status' => 'successful',
            'last_payment_amount' => $amount,
        ]);

        return redirect()->route('home')->with('success', 'Payment successful! Thank you for your purchase.');
    }

    public function checkoutCancel()
    {
        return redirect()->route('home')->with('error', 'Payment was cancelled. Please try again.');
    }

    private function handleSubscription($session)
    {

    }

    private function cancelSubscription()
    {
        $user =auth()->user();

        $user->subscription()->cancel();

        return redirect()->back();
    }
}
