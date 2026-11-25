<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles\UserRoles;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Price;
use App\Models\Plan;
use App\Models\Subscription as BillingSubscription;
use App\Models\Course;
use App\Models\Liveshow;
use App\Models\LiveCoachingBooking;
use App\Models\PersonalizedGuidanceRequest;
use App\Models\PersonalizedPlan;
use Illuminate\Support\Facades\Log;
use App\Models\Upload;
use App\Models\UserDailyLogin;
use App\Models\UserAssessment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Validator;

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
            $user = Auth::user();
            $userId = $user->id;

            // Mark the user as active today (covers persisted/"remember me" sessions
            // as well as fresh logins, since this dashboard is where today's
            // activity is recorded for the practice-streak grid).
            UserDailyLogin::recordToday($userId, $user->timezone);

            $latestCourses = [];

            $latestPianoExercise = Upload::where('category', 'piano exercise')
                ->orderBy('created_at', 'desc')
                ->first();
            // Etudes & Pieces share the Piano Exercise slot: show whichever is newest.
            $latestEtude = \App\Models\Etude::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->first();
            if ($latestEtude && (! $latestPianoExercise || $latestEtude->created_at > $latestPianoExercise->created_at)) {
                $latestPianoExercise = $latestEtude;
            }
            if ($latestPianoExercise) {
                $latestCourses['piano exercise'] = $latestPianoExercise;
            }

            $latestGuidedPractice = \App\Models\MusicalApplication::orderBy('created_at', 'desc')->first();
            if ($latestGuidedPractice) {
                $latestCourses['guided practice'] = $latestGuidedPractice;
            }

            $latestLearnSong = \App\Models\LearnSong::orderBy('created_at', 'desc')->first();
            if ($latestLearnSong) {
                $latestCourses['learn songs'] = $latestLearnSong;
            }

            $latestExtraCourse = \App\Models\ExtraCourse::orderBy('created_at', 'desc')->first();
            if ($latestExtraCourse) {
                $latestCourses['extra courses'] = $latestExtraCourse;
            }

            $resumeLesson = $this->buildResumeLesson($userId);
            $personalizedPlan = $user->premium ? PersonalizedPlan::where('user_id', $userId)->first() : null;
            $nextPractice = $personalizedPlan?->nextUnfinishedLesson();
            $planPending = $user->premium && !$personalizedPlan
                && (PersonalizedGuidanceRequest::where('user_id', $userId)->exists()
                    || LiveCoachingBooking::where('user_id', $userId)->exists());

            $dashboardLiveshow = Liveshow::whereIn('access_type', $user->premium ? ['all', 'premium'] : ['all'])
                ->where('category', 'event')->where('start_time', '>=', now())
                ->orderBy('start_time')->first();

            return view('home', compact(
                'latestCourses', 'resumeLesson', 'personalizedPlan', 'nextPractice',
                'planPending', 'dashboardLiveshow'
            ));
        }
    }


    /**
     * Finds the most recently viewed lesson (across all lesson types) so the
     * dashboard can offer a "Resume Lesson" shortcut instead of "Get Started"
     * once the member has actually started learning.
     */
    private function buildResumeLesson($userId): ?array
    {
        $recentViews = \App\Models\LessonView::where('user_id', $userId)
            ->latest('updated_at')
            ->limit(10)
            ->with('viewable')
            ->get();

        foreach ($recentViews as $view) {
            if (!$view->viewable) {
                continue;
            }

            $resolved = $this->resolveResumeLesson($view->viewable, $userId);
            if ($resolved) {
                return $resolved;
            }
        }

        return null;
    }

    private function resolveResumeLesson($lesson, int $userId): ?array
    {
        if ($lesson instanceof Course) {
            $level = strtolower($lesson->level);
            $roadmap = Course::where('level', $level)->where('status', 'active');
            $total = (clone $roadmap)->count();
            $completed = (clone $roadmap)->whereIn('id', DB::table('course_progress')
                ->select('course_id')->where('user_id', $userId))->count();

            return [
                'title' => $lesson->title,
                'url' => url("/member/course/{$level}/{$lesson->id}"),
                'context' => ucfirst($level) . ' roadmap',
                'progress' => $total ? ['completed' => $completed, 'total' => $total] : null,
            ];
        }

        return match (get_class($lesson)) {
            Upload::class => [
                'title' => $lesson->title,
                'context' => 'Piano Exercise',
                'url' => route('piano.exercise.player', ['level' => $lesson->level, 'video_id' => $lesson->id]),
            ],
            \App\Models\MusicalApplication::class => [
                'title' => $lesson->title,
                'context' => 'Guided Practice',
                'url' => route('piano.exercise.player', [
                    'series' => $lesson->series,
                    'skill_level' => strtolower($lesson->skill_level),
                    'video_id' => $lesson->id,
                ]),
            ],
            \App\Models\ExtraCourse::class => [
                'title' => $lesson->title,
                'context' => 'Extra Courses',
                'url' => url("/member/lesson/{$lesson->id}?type=extra_course"),
            ],
            \App\Models\LearnSong::class => [
                'title' => $lesson->title,
                'context' => 'Learn Songs',
                'url' => url("/member/lesson/{$lesson->id}?type=learn_song"),
            ],
            \App\Models\Etude::class => [
                'title' => $lesson->title,
                'context' => 'Etudes & Pieces',
                'url' => url("/member/lesson/{$lesson->id}?type=etudes"),
            ],
            default => null,
        };
    }

    public function admin(Request $request)
    {
        abort_unless($request->user()->role === UserRoles::ADMIN->value, 403);

        $tabs = [
            'overview' => 'Overview',
            'acquisition' => 'Audience',
            'student-engagement' => 'Students',
            'subscriptions' => 'Subscription Activity',
        ];
        $requestedTab = $request->query('tab');
        if ($requestedTab === 'students-resources') {
            $requestedTab = 'student-engagement';
        }
        $activeTab = is_string($requestedTab) && isset($tabs[$requestedTab]) ? $requestedTab : 'overview';

        $today = CarbonImmutable::today();
        $dates = Validator::make($request->only('from', 'to'), [
            'from' => ['required_with:to', 'date_format:Y-m-d', 'before_or_equal:to'],
            'to' => ['required_with:from', 'date_format:Y-m-d', 'before_or_equal:' . $today->toDateString()],
        ]);

        $from = $today->subDays(29);
        $to = $today;
        $rangeError = null;
        if ($request->hasAny(['from', 'to'])) {
            if ($dates->fails() || !$request->filled(['from', 'to'])) {
                $rangeError = 'Choose a valid start and end date up to today.';
            } elseif (CarbonImmutable::parse($request->input('from'))->diffInDays($request->input('to')) > 365) {
                $rangeError = 'Choose a range of up to 366 days.';
            } else {
                $from = CarbonImmutable::parse($request->input('from'));
                $to = CarbonImmutable::parse($request->input('to'));
            }
        }

        $window = [$from, $to->endOfDay()];
        $dayWindow = [$from->toDateString(), $to->toDateString()];
        $members = User::query()->where('role', UserRoles::MEMBER->value);
        $signups = (clone $members)->whereBetween('created_at', $window);
        $activity = UserDailyLogin::query()
            ->whereBetween('date', $dayWindow)
            ->whereIn('user_id', (clone $members)->select('id'));
        $guideLeads = DB::table('piano_guide_leads')->whereBetween('created_at', $window);
        $roadmapEmails = DB::table('visitors')->where('source', 'visitor_page')
            ->whereBetween('created_at', $window)->selectRaw('LOWER(TRIM(email)) as email');
        $pdfEmails = (clone $guideLeads)->selectRaw('LOWER(TRIM(email)) as email')->union($roadmapEmails);
        $pdfRequesters = DB::query()->fromSub($pdfEmails, 'pdf_emails')->count();
        $periodDays = (int) $from->diffInDays($to) + 1;
        $demoTraffic = [];
        foreach ([
            'Google Search' => [37, 72, 19],
            'YouTube' => [30, 65, 24],
            'Instagram' => [21, 38, 9],
            'TikTok' => [12, 19, 4],
            'Direct link' => [10, 24, 7],
            'WhatsApp' => [7, 19, 8],
            'Email' => [4, 14, 6],
            'Facebook' => [3, 5, 1],
        ] as $source => [$dailyVisitors, $monthlyAccounts, $monthlyPaidPlans]) {
            $demoTraffic[$source] = [
                'visitors' => $dailyVisitors * $periodDays,
                'accounts' => (int) round($monthlyAccounts * $periodDays / 30),
                'paidPlans' => (int) round($monthlyPaidPlans * $periodDays / 30),
            ];
        }
        $demoVisitors = array_sum(array_column($demoTraffic, 'visitors'));

        $stats = [
            'New sign-ups' => ['users', (clone $signups)->count(), 'Member accounts created'],
            'Active members' => ['sign-in-alt', (clone $activity)->distinct()->count('user_id'), 'Members with a recorded active day'],
            'PDF requesters' => ['file-alt', $pdfRequesters, 'Unique emails from free guide and roadmap requests'],
            'Visitors' => ['globe', $demoVisitors, 'Estimated unique visitors for this period'],
        ];

        $signupDays = (clone $signups)->selectRaw('DATE(created_at) as day, COUNT(*) as total')->groupByRaw('DATE(created_at)')->pluck('total', 'day');
        $signInDays = DB::table('member_sign_in_days');
        $firstRecordedSignIn = (clone $signInDays)->min('date');
        $signInsByDate = $signInDays->whereBetween('date', $dayWindow)
            ->selectRaw('date, COUNT(*) as total')->groupBy('date')->pluck('total', 'date');
        $dailyCounts = [];
        for ($day = $to; $day->gte($from); $day = $day->subDay()) {
            $date = $day->toDateString();
            $dailyCounts[] = [
                'date' => $day->format('D, M j, Y'),
                'signups' => $signupDays[$date] ?? 0,
                'signIns' => $firstRecordedSignIn && $date >= $firstRecordedSignIn ? ($signInsByDate[$date] ?? 0) : null,
            ];
        }
        $guides = $guideLeads->selectRaw('guide_slug, COUNT(*) as total')->groupBy('guide_slug')->orderByDesc('total')->orderBy('guide_slug')->limit(6)->get();
        $latestAssessments = UserAssessment::query()
            ->whereIn('user_id', (clone $members)->select('id'))
            ->selectRaw('MAX(id)')->groupBy('user_id');
        $assessmentCounts = UserAssessment::query()->whereIn('id', $latestAssessments)
            ->selectRaw('LOWER(skill_level) as level, COUNT(*) as total')->groupByRaw('LOWER(skill_level)')
            ->pluck('total', 'level');
        $skillLevels = [];
        foreach (['Beginner', 'Intermediate', 'Advanced'] as $level) {
            $skillLevels[$level] = (int) ($assessmentCounts[strtolower($level)] ?? 0);
        }

        return view('admin.home', compact(
            'tabs', 'activeTab',
            'from', 'to', 'today', 'rangeError', 'stats', 'dailyCounts', 'guides', 'skillLevels', 'demoTraffic', 'demoVisitors'
        ));
    }

    public function profile()
    {
        $user = auth()->user();
        Stripe::setApiKey(config('cashier.secret'));

        $metadata = $user->metadata ?? $user->metadata ?? [];
        $transactions = BillingSubscription::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function (BillingSubscription $subscription) use ($user, $metadata) {
                $provider = $user->subscriptionProvider($subscription);
                $amount = $user->last_payment_amount ?? $user->last_payment_amount;
                $currency = strtoupper((string) data_get($metadata, 'currency', 'USD'));
                $interval = $subscription->duration
                    ?? data_get($metadata, 'duration')
                    ?? $user->subscription_type
                    ?? $user->subscription_type;

                $planId = $subscription->plan_code ?? $subscription->plan_code;
                $plan = is_numeric($planId) ? Plan::find($planId) : null;

                if ($plan) {
                    $currency = strtoupper((string) data_get($metadata, 'currency', $currency));
                    $amount = match ($currency) {
                        'NGN' => $plan->price_ngn ?? $plan->price_ngn,
                        'EUR' => $plan->price_eur,
                        default => $plan->price_usd,
                    };
                    $interval = $plan->type ?: $interval;
                }

                $priceId = $subscription->stripe_price
                    ?? $subscription->stripe_price
                    ?? DB::table('subscription_items')
                        ->where('subscription_id', $subscription->id)
                        ->value('stripe_price');

                if ($provider === 'stripe' && is_string($priceId) && str_starts_with($priceId, 'price_')) {
                    try {
                        $stripePrice = Price::retrieve($priceId);
                        $amount = ($stripePrice->unit_amount ?? 0) / 100;
                        $currency = strtoupper((string) ($stripePrice->currency ?? $currency));
                        $interval = $stripePrice->recurring->interval ?? $interval;
                    } catch (\Throwable $e) {
                        Log::warning('Unable to load Stripe price for profile', [
                            'subscription_id' => $subscription->id,
                            'price_id' => $priceId,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $intervalKey = strtolower((string) $interval);
                $displayInterval = match ($intervalKey) {
                    'month', 'monthly' => 'month',
                    'quarter', 'quarterly' => 'quarter',
                    'year', 'yearly' => 'year',
                    default => $intervalKey ?: 'month',
                };

                $tier = strtolower((string) (
                    data_get($metadata, 'tier')
                    ?? (($user->premium || $user->is_premium) ? 'premium' : 'standard')
                ));
                $isPremium = str_contains($tier, 'premium');
                $planName = match ($displayInterval) {
                    'month' => $isPremium ? 'Monthly Premium Plan' : 'Monthly Standard Plan',
                    'quarter' => $isPremium ? 'Quarterly Premium Plan' : 'Quarterly Standard Plan',
                    'year' => $isPremium ? 'Yearly Premium Plan' : 'Yearly Standard Plan',
                    default => $isPremium ? 'Premium Plan' : 'Standard Plan',
                };

                return (object) [
                    'name' => $planName,
                    'amount' => $amount,
                    'currency' => $currency,
                    'interval' => $displayInterval,
                    'starts_at' => $subscription->created_at,
                    'ends_at' => $subscription->ends_at,
                    'stripe_status' => $subscription->stripe_status,
                    'payment_method' => $provider,
                ];
            });

        $latestSubscription = $transactions->first();
        $canCancelSubscription = (bool) $user->cancellableSubscription();
        $onGracePeriod = $user->subscriptionOnGracePeriod();
        $graceEndsAt = $user->subscription_expires_at
            ?? $user->latestLocalSubscription()?->ends_at;
        $providerLabel = match ($user->subscriptionProvider()) {
            'paypal' => 'PayPal',
            'paystack' => 'Paystack',
            default => 'Stripe',
        };

        $countries = DB::table('countries')
            ->orderBy('country_name')
            ->pluck('country_name', 'country_code');

        return view('memberpages.profile', compact(
            'transactions',
            'countries',
            'latestSubscription',
            'canCancelSubscription',
            'onGracePeriod',
            'graceEndsAt',
            'providerLabel'
        ));
    }

    public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'new_password_input' => 'nullable|string|min:8|confirmed',
        'passport' => 'nullable|image|mimes:jpeg,png,jpg',
        'country' => 'nullable|string|max:500',
        'biography' => 'nullable|string|max:1000',
    ], [
        'new_password_input.confirmed' => 'The new password and confirm password fields do not match.',
        'new_password_input.min' => 'The new password must be at least 8 characters.',
    ]);

    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;
    $user->country = $request->country;
    $user->biography = $request->biography;

    if ($request->filled('new_password_input')) {
        $user->password = Hash::make($request->new_password_input);
    }

    /** ------------------------------------
     * PASSPORT UPLOAD → public_html/passports
     * ------------------------------------*/
    if ($request->hasFile('passport')) {

        // Delete old passport if exists
        if ($user->passport) {
            $oldPath = base_path('../public_html' . $user->passport);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $file = $request->file('passport');
        $filename = time() . '_' . $file->getClientOriginalName();

        // Full path to public_html/passports
        $destinationPath = base_path('../public_html/passports');

        // Create folder if not exists
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Move file to public_html
        $file->move($destinationPath, $filename);

        // Save path relative to public_html
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

    public function updateWhatsappPreference(Request $request)
    {
        $user = User::find(Auth::id());
        $metadata = is_array($user->metadata) ? $user->metadata : [];
        
        $action = $request->action;
        
        if ($action === 'join') {
            $metadata['whatsapp_joined'] = true;
        } elseif ($action === 'remind') {
            $duration = $request->duration;
            $remindAt = match($duration) {
                '1h' => now()->addHour(),
                '3h' => now()->addHours(3),
                '6h' => now()->addHours(6),
                'tomorrow' => now()->addDay()->startOfDay()->addHours(9),
                '3d' => now()->addDays(3),
                '1w' => now()->addWeek(),
                default => now()->addHour(),
            };
            $metadata['whatsapp_remind_at'] = $remindAt->toDateTimeString();
        } elseif ($action === 'dismiss') {
            $metadata['whatsapp_remind_at'] = now()->addWeek()->toDateTimeString();
        }
        
        $user->metadata = $metadata;
        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * Store the browser-detected IANA timezone for the authenticated user,
     * so day-based stats (e.g. the practice streak) use their local time.
     */
    public function updateTimezone(Request $request)
    {
        $request->validate(['timezone' => 'required|string|max:64']);

        if (in_array($request->timezone, \DateTimeZone::listIdentifiers(), true)) {
            Auth::user()->update(['timezone' => $request->timezone]);
        }

        return response()->json(['success' => true]);
    }
}
