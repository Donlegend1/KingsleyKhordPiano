<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

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

        return $request->user()
            ->newSubscription('default', $plan->stripe_product_id)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('checkout.success'),
                'cancel_url' => route('checkout.cancel'),
                'metadata' => [
                    'user_id' => $request->user()->id,
                    'tier' => $request->tier,
                    'duration' => $request->duration,
                ]
            ]);
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

        try {
            if (($user->payment_method === 'paypal')
                || optional($user->subscription('default'))->payment_method === 'paypal'
            ) {
                app(\App\Services\PayPalService::class)->cancelSubscription($user);
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\SubscriptionCanceledMail($user));

                return back()->with('success', 'PayPal subscription cancelled. You keep access until the end of the billing period.');
            }

            if ($user->subscription('default')) {
                $user->subscription('default')->cancel();
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\SubscriptionCanceledMail($user));
            }

            return back()->with('success', 'Subscription cancelled.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Subscription cancel failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Unable to cancel subscription. Please try again.');
        }
    }

    
}
