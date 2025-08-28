<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles\UserRoles;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Called automatically after a successful login.
     */
    protected function authenticated(Request $request, $user)
    {
        $user->update([
            'last_login_at' => now()
        ]);
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
