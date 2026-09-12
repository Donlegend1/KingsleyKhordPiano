<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateLastSeen
{
    /**
     * Keep last_login_at fresh while a member is actively browsing, not just
     * at login, so "Online" counts reflect real recent activity.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (! $user->last_login_at || $user->last_login_at->lt(now()->subMinutes(5))) {
                $user->forceFill(['last_login_at' => now()])->saveQuietly();
            }
        }

        return $next($request);
    }
}
