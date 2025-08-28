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

        if ($user->role === 'member') {
            $activePayment = $user->payments()->where('status', 'successful')->where('ends_at', '>', now())->first();
            if (in_array($user->payment_status, ['pending', 'expired']) || !$activePayment) {
                return redirect('/member/plan');
            }

            $latestPayment = $user->payments()->latest('ends_at')->first();

            if (!$latestPayment || $latestPayment->ends_at->isPast()) {
                $user->update(['payment_status' => 'expired']);
                return redirect('/member/plan');
            }
        }

        return $next($request);
    }
}
