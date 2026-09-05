<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PayPalController extends Controller
{
    public function __construct(protected PayPalService $paypalService)
    {
    }

    public function pay(Request $request)
    {
        $request->validate([
            'plan_id' => 'nullable|integer|exists:plans,id',
            'tier' => 'required|string',
            'duration' => 'required|in:daily,monthly,quarterly,yearly',
            'currency' => 'required|in:USD,EUR',
        ]);

        $user = Auth::user();
        if (! $user) {
            return redirect()->route('register');
        }

        if ($user->hasActiveSubscription()) {
            return redirect()->back()->with('error', 'You already have an active subscription.');
        }

        try {
            $plan = $request->filled('plan_id')
                ? Plan::findOrFail($request->plan_id)
                : Plan::where('type', $request->duration)
                    ->where('tier', $request->tier)
                    ->first();

            if (! $plan) {
                return redirect()->back()->with('error', 'Selected plan not found.');
            }

            // Prefer looking up by plan_id; still validate duration matches.
            if ($plan->type !== $request->duration) {
                return redirect()->back()->with('error', 'Selected plan does not match the chosen duration.');
            }

            $result = $this->paypalService->createSubscription(
                $user,
                $plan,
                strtoupper($request->currency),
                $request->tier,
                $request->duration
            );

            return redirect()->away($result['approve_url']);
        } catch (\Throwable $e) {
            Log::error('PayPal checkout failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Unable to start PayPal subscription. Please try again.');
        }
    }

    public function success(Request $request)
    {
        $subscriptionId = $request->query('subscription_id')
            ?? $request->query('ba_token')
            ?? $request->input('subscription_id');

        // PayPal Billing returns subscription_id on the return URL.
        if (! $subscriptionId) {
            return redirect()->route('subscription.page')
                ->with('error', 'Missing PayPal subscription reference.');
        }

        try {
            $this->paypalService->activateFromReturn($subscriptionId);

            return redirect()->route('community.index')
                ->with('success', 'Payment successful and subscription activated!');
        } catch (\Throwable $e) {
            Log::error('PayPal activation failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('subscription.page')
                ->with('error', $e->getMessage());
        }
    }

    public function error()
    {
        return redirect()->route('subscription.page')
            ->with('error', 'PayPal checkout was cancelled.');
    }
}
