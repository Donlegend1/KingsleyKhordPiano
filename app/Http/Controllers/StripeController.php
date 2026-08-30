<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionCanceledMail;
use App\Models\Plan;
use App\Services\PayPalService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'tier' => 'required|string',
            'duration' => 'required|in:monthly,quarterly,yearly',
            'plan_id' => 'required|integer'
        ]);

        if ($request->user()->hasActiveSubscription()) {
            return redirect()->back()->with('error', 'You already have an active subscription.');
        }

        $plan = Plan::find($request->plan_id);

        $user = $request->user()
            ->newSubscription('default', $plan->stripe_product_id)
            ->withMetadata([
                'user_id' => $request->user()->id,
                'plan_id' => $plan->id,
                'tier' => $request->tier,
                'duration' => $request->duration,
            ])
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('checkout.success'),
                'cancel_url' => route('checkout.cancel'),
                'metadata' => [
                    'user_id' => $request->user()->id,
                    'plan_id' => $plan->id,
                    'tier' => $request->tier,
                    'duration' => $request->duration,
                ]
            ]);

        return $user;
    }

    public function checkoutSuccess()
    {
        return redirect()->route('home')->with('success', 'Subscription successful!');
    }

    public function checkoutCancel()
    {
        return redirect()->route('home')->with('error', 'Checkout cancelled.');
    }

    public function cancelSubscription()
    {
        $user = auth()->user();

        if (! $user) {
            return back()->with('error', 'You must be logged in.');
        }

        $subscription = $user->cancellableSubscription();
        if (! $subscription) {
            return back()->with('error', 'No active subscription to cancel.');
        }

        $provider = $user->subscriptionProvider($subscription);

        try {
            if ($provider === 'paypal') {
                app(PayPalService::class)->cancelSubscription($user);
                $message = 'Your PayPal subscription has been cancelled. You keep access until the end of the billing period.';
            } else {
                app(StripeService::class)->cancelSubscription($user);
                $message = 'Your Stripe subscription has been cancelled. You keep access until the end of the billing period.';
            }

            Mail::to($user->email)->send(new SubscriptionCanceledMail($user));

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Subscription cancel failed', [
                'user_id' => $user->id,
                'provider' => $provider,
                'subscription_id' => $subscription->stripe_id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Unable to cancel subscription. Please try again.');
        }
    }

    
}
