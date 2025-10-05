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
            // Prevent access if user does not have an active Cashier subscription
            if (!$user->subscribed('default')) {
                return redirect('/member/plan');
            }
        }

        return $next($request);
    }
}
