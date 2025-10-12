<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles\UserRoles;
use App\Models\User;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\Price;
use Laravel\Cashier\Subscription;
use App\Models\Course;
use App\Models\Upload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::user()->role == UserRoles::MEMBER->value) {
            $userId = Auth::id();

            // Define levels
            $levels = ['Beginner', 'Intermediate', 'Advanced'];

            $progress = collect($levels)->mapWithKeys(function ($level) use ($userId) {
                $total = DB::table('courses')
                    ->where('level', $level)
                    ->count();

                $completed = DB::table('course_progress')
                    ->join('courses', 'course_progress.course_id', '=', 'courses.id')
                    ->where('course_progress.user_id', $userId)
                    ->where('courses.level', $level)
                    ->distinct('course_progress.course_id')
                    ->count('course_progress.course_id');

                return [$level => [
                    'total' => $total,
                    'completed' => $completed,
                ]];
            });

        $categories = ['piano exercise', 'extra courses', 'quick lessons', 'learn songs'];

            $latestCourses = [];

            foreach ($categories as $category) {
                $course = Upload::
                    where('category', $category)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($course) {
                    $latestCourses[$category] = $course;
                }
            }
            // dd( $latestCourses);
            return view('home', compact('progress', 'levels', 'latestCourses'));
        }
    }

    public function admin()
    {
        if (Auth::user()->role == UserRoles::ADMIN->value) {
            $users = User::where('created_at', '>=', Carbon::now()->subWeeks(2))->paginate(20);
            $usdRevenue = Payment::where('status', 'successful')
            ->whereRaw("JSON_EXTRACT(metadata, '$.currency') = 'USD'")
            ->sum(DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.amount')) AS UNSIGNED)"));

        $eurRevenue = Payment::where('status', 'successful')
            ->whereRaw("JSON_EXTRACT(metadata, '$.currency') = 'EUR'")
            ->sum(DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.amount')) AS UNSIGNED)"));

        $nairaRevenue = Payment::where('status', 'successful')
            ->whereRaw("JSON_EXTRACT(metadata, '$.currency') = 'NGN'")
            ->sum(DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.amount')) AS UNSIGNED)"));
            
            $courses =Course::count();

            return view('admin.home', compact('users', 'usdRevenue', 'eurRevenue', 'nairaRevenue', 'courses'));
        }
    }

    public function profile()
    {
        Stripe::setApiKey(config('cashier.secret'));

        $transactions = Subscription::with('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(function ($subscription) {
                $item = $subscription->items->first();
                $priceAmount = null;
                $currency = 'USD';
                $interval = null;

                if ($item && $item->stripe_price) {
                    $stripePrice = Price::retrieve($item->stripe_price);
                    $priceAmount = $stripePrice->unit_amount / 100;
                    $currency = strtoupper($stripePrice->currency);
                    $interval = $stripePrice->recurring->interval ?? null;
                }

                return (object) [
                    'name' => $subscription->name,
                    'amount' => $priceAmount,
                    'currency' => $currency,
                    'interval' => $interval,
                    'starts_at' => $subscription->created_at,
                    'stripe_status' => $subscription->stripe_status,
                ];
            });

            // dd($transactions);

        $countries = DB::table('countries')
            ->orderBy('country_name')
            ->pluck('country_name', 'country_code');

        return view('memberpages.profile', compact('transactions', 'countries'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'passport' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'country' => 'nullable|string|max:500',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->country = $request->country;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('passport')) {
            // Delete old file if exists
            if ($user->passport) {
                $oldPath = public_path($user->passport);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('passport');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('passports');

            // Ensure the directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            // Save full relative path
            $user->passport = '/passports/' . $filename;
        }

        $user->save();

        return redirect('/home')->with('success', 'Profile updated.');
    }

    public function destroy()
    {
        $user = Auth::user();
        $user->delete();

        return redirect('/')->with('success', 'Account deleted.');
    }

    public function support()
    {
       return view('memberpages.support');
    }

    function handleGetStarted() 
    {
        $user = User::find(Auth::user()->id);
        $user->metadata = ['hide_get_started' => true];
        $user->save();
        return redirect()->back();
    }
}
