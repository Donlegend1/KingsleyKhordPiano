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
            'duration' => 'required|in:monthly,yearly',
        ]);

        $plan = Plan::where('tier', $request->tier)
            ->where('type', $request->duration)
            ->firstOrFail();

        return $request->user()
            ->newSubscription('default', $plan->price_id)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('checkout.success'),
                'cancel_url' => route('checkout.cancel'),
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

        if ($user->subscription('default')) {
            $user->subscription('default')->cancel();
        }

        return back()->with('success', 'Subscription cancelled.');
    }
}
