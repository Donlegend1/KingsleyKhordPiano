<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Enums\Roles\UserRoles;
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
            // Reuse the same rules and messages as the traditional form
            $validator = Validator::make(
                $request->all(),
                $this->rules(),
                $this->messages()
            );

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
                'role' => UserRoles::MEMBER->value,
                'payment_status' => 'pending',
                'terms_accepted_at' => now(),
            ]);

            // 🔥 This sends the verification email
            event(new Registered($user));

            auth()->login($user);

            $redirectUrl = $this->redirectPath();
            if ($request->has('plan_id')) {
                $redirectUrl = route('payment.direct-checkout', $request->only(['plan_id', 'currency', 'tier', 'duration', 'payment_method']));
            }

            return response()->json([
                'success' => true,
                'user' => $user,
                'redirect_url' => $redirectUrl,
            ]);
        }

        // Fallback (non-AJAX)
        $this->validator($request->all())->validate();

        // create() already fires the Registered event, so it is not fired again here
        $user = $this->create($request->all());

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return redirect($this->redirectPath());
    }

    /**
     * Validation rules shared by the AJAX and traditional flows.
     */
    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
            'terms' => ['accepted'],
            'g-recaptcha-response' => ['required', 'captcha'],
            // 'plan' => ['required', 'string'],
        ];
    }

    /**
     * Custom validation messages.
     */
    protected function messages(): array
    {
        return [
            'terms.accepted' => 'You must agree to the Terms of Service and Privacy Policy.',
        ];
    }

    /**
     * For traditional form validation.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, $this->rules(), $this->messages());
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
            'terms_accepted_at' => now(),
            'payment_status' => 'pending',
        ]);

        event(new Registered($user));

        return $user;
    }

    /**
     * The user has been registered.
     */
    protected function registered(Request $request, $user)
    {
        if ($request->has('plan_id')) {
            return app(\App\Http\Controllers\PaymentController::class)->directCheckout($request);
        }

        return redirect($this->redirectTo);
    }
}