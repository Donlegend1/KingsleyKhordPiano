<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPaymentStatus
{
    /**
     * Members need an active Stripe, PayPal, Paystack, or manual subscription.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'member') {
            return $next($request);
        }

        if ($user->hasActiveSubscription()) {
            return $next($request);
        }

        if ($user->hasPendingStripeCheckout()) {
            return redirect()
                ->route('subscription.page')
                ->with('info', 'Your subscription is still being processed. Please wait a moment.');
        }

        return redirect()->route('subscription.page');
    }
}
