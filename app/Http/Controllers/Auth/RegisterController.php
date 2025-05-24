<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle the registration request (supports both AJAX and traditional).
     */
    public function register(Request $request)
    {
        // If request is AJAX (from Alpine/JS)
        \Log::info($request->all());
        if ($request->expectsJson()) {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                // 'plan' => ['required'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                // 'plan' => $request->plan,
                'role' => UserRole::MEMBER->value,
                'payment_status' => 'pending',
            ]);

            auth()->login($user);

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        }

        // Fallback: standard Laravel flow
        $this->validator($request->all())->validate();
        $user = $this->create($request->all());

        $this->guard()->login($user);

        return redirect($this->redirectPath());
    }

    /**
     * For traditional form validation.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 'plan' => ['required', 'string'],
        ]);
    }

    /**
     * Create a new user instance (for fallback form).
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            // 'plan' => $data['plan'],
            'payment_status' => 'pending',
        ]);
    }
}
