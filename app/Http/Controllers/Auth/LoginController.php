<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles\UserRoles;
use App\Models\UserDailyLogin;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        $user->update([
            'last_login_at' => now()
        ]);

        UserDailyLogin::recordToday($user->id, $user->timezone);

        if ($request->has('plan_id')) {
            return app(\App\Http\Controllers\PaymentController::class)->directCheckout($request);
        }
    }

    /**
     * Redirect users based on role.
     */
    protected function redirectTo()
    {
        $user = Auth::user();

        if ($user->role === UserRoles::ADMIN->value) {
            return '/admin/dashboard';
        }

        return '/home';
    }
}
