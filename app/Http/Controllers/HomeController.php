<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles\UserRoles;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


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

        if (Auth::user()->role == UserRoles::ADMIN->value) {
            $users = User::all();
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
        if (Auth::user()->role == UserRoles::MEMBER->value) {
            $completedCourses = DB::table('course_progress')
                ->join('courses', 'course_progress.course_id', '=', 'courses.id')
                ->where('course_progress.user_id', Auth::id())
                ->select(
                    'courses.id as course_id',
                    'courses.title as course_title',
                    'courses.category as course_category',
                    'courses.level as course_level',
                    'courses.status as course_status',
                    DB::raw('COUNT(course_progress.id) as completion_count')
                )
                ->groupBy('courses.id', 'courses.title', 'courses.category', 'courses.level', 'courses.status')
                ->get();
             $userId = Auth::id();

            // Step 1: Get all recent progress entries
            $recentProgress = DB::table('course_progress')
                ->join('courses', 'course_progress.course_id', '=', 'courses.id')
                ->where('course_progress.user_id', $userId)
                ->orderBy('course_progress.created_at', 'desc')
                ->select('courses.category', 'courses.id as course_id')
                ->get();

        // Step 2: Extract the latest 2 unique categories
        $latestCategories = $recentProgress->pluck('category')->unique()->take(2);

        // Step 3: Get total number of courses per category
        $categoryTotals = DB::table('courses')
            ->whereIn('category', $latestCategories)
            ->select('category', DB::raw('COUNT(*) as total_courses'))
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        // Step 4: Get number of completed courses by the user per category
        $categoryCompleted = DB::table('course_progress')
            ->join('courses', 'course_progress.course_id', '=', 'courses.id')
            ->where('course_progress.user_id', $userId)
            ->whereIn('courses.category', $latestCategories)
            ->select('courses.category', DB::raw('COUNT(DISTINCT courses.id) as completed_courses'))
            ->groupBy('courses.category')
            ->get()
            ->keyBy('category');

        $categoryProgress = [];

        foreach ($latestCategories as $category) {
            $completed = $categoryCompleted[$category]->completed_courses ?? 0;
            $total = $categoryTotals[$category]->total_courses ?? 1; // avoid division by zero
            $categoryProgress[] = [
                'course_category' => $category,
                'completed_courses' => $completed,
                'total_courses' => $total,
                'level' => $completedCourses->firstWhere('course_category', $category)->course_level ?? 'N/A',
                'completion_percentage' => round(($completed / $total) * 100, 1),
            ];
        }

            // dd($categoryProgress);
            return view('home', compact('completedCourses', 'categoryProgress'));
        }
        
    }

    function profile() {
        $subscriptions = Subscription::all();

        $transactions = Payment::where('user_id', auth()->user()->id)->get();
        return view('memberpages.profile', compact('subscriptions', 'transactions'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password); 
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

    function handleGetStarted() : Returntype {
        $user = User::find(Auth::user()->id);
        $user->metadata = ['hide_get_started' => true];
        $user->save();
        return redirect()->back();
    }
}
