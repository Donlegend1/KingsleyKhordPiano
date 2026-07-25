<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPaymentStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->role === 'member') {

            $hasActiveSubscription = $user->subscribed('default');

            // Block if stripe subscription exists but is still pending
            $hasPendingStripeSubscription = $user->subscriptions()
                ->where('stripe_status', 'pending')
                ->exists();

            if ($hasPendingStripeSubscription) {
                return redirect('/member/plan')->with('info', 'Your subscription is still being processed. Please wait a moment.');
            }

            $activePayment = $user->payments()
                ->where('status', 'successful')
                ->where('ends_at', '>', now())
                ->first();

            $latestPayment = $user->payments()->latest('ends_at')->first();

            $isLegacyExpired = false;

            if ($latestPayment && $latestPayment->ends_at->isPast()) {
                $user->update(['payment_status' => 'expired']);
                $isLegacyExpired = true;
            }

            $hasLegacyAccess = !in_array($user->payment_status, ['pending', 'expired']) && $activePayment;

            $hasAccess = $hasActiveSubscription || $hasLegacyAccess;

            if (! $hasAccess) {
                return redirect('/member/plan');
            }
        }

        return $next($request);
    }
}
