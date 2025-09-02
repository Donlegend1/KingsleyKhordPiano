<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Notifications\WelcomeEmailNotification;


class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register(Request $request)
    {

        if ($request->expectsJson()) {
            $validator = Validator::make($request->all(), [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:4', 'confirmed'],
                'g-recaptcha-response' => ['required', 'captcha'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => UserRole::MEMBER->value,
                'payment_status' => 'pending',
            ]);

            // 🔥 This sends the verification email
            event(new Registered($user));

            auth()->login($user);

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        }

        // Fallback (non-AJAX)
        $this->validator($request->all())->validate();
        $user = $this->create($request->all());

        // 🔥 Again, ensure the event is triggered here too
        event(new Registered($user));

        $user->notify(new WelcomeEmailNotification($user));
            

        $this->guard()->login($user);

        return redirect($this->redirectPath());
    }

    /**
     * For traditional form validation.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
            'g-recaptcha-response' => ['required', 'captcha'],
            // 'plan' => ['required', 'string'],
        ]);
    }

    /**
     * Create a new user instance (for fallback form).
     */
    protected function create(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'payment_status' => 'pending',
        ]);

        event(new Registered($user)); 
        $user->notify(new WelcomeEmailNotification($user));

        return $user;
    }
}
