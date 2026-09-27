<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PersonalizedGuidanceReady;
use App\Models\LiveCoachingBooking;
use App\Models\PersonalizedGuidanceRequest;
use App\Models\PersonalizedPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PersonalizedGuidanceController extends Controller
{
    public function index()
    {
        $requests = PersonalizedGuidanceRequest::with('user')->get()->keyBy('user_id');
        $bookings = LiveCoachingBooking::with('user')->orderByDesc('date')->orderByDesc('time')->get()->groupBy('user_id');

        $userIds = $requests->keys()->merge($bookings->keys())->unique();

        $entries = $userIds->map(function ($userId) use ($requests, $bookings) {
            $request = $requests->get($userId);
            $userBookings = $bookings->get($userId, collect());

            return (object) [
                'user' => optional($request)->user ?? optional($userBookings->first())->user,
                'request' => $request,
                'bookings' => $userBookings,
                'latestAt' => collect([
                    optional($request)->created_at,
                    optional($userBookings->first())->created_at,
                ])->filter()->max(),
            ];
        })->filter(fn ($entry) => $entry->user)->sortByDesc('latestAt')->values();

        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginated = new LengthAwarePaginator(
            $entries->forPage($page, $perPage)->values(),
            $entries->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.personalized_guidance.index', ['entries' => $paginated]);
    }

    public function show(User $user)
    {
        $guidanceRequest = PersonalizedGuidanceRequest::where('user_id', $user->id)->latest()->first();
        $bookings = LiveCoachingBooking::where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        return view('admin.personalized_guidance.show', [
            'user' => $user,
            'request' => $guidanceRequest,
            'bookings' => $bookings,
        ]);
    }

    public function markReviewed(PersonalizedGuidanceRequest $guidanceRequest)
    {
        $guidanceRequest->update(['status' => 'reviewed']);
        return back()->with('success', 'Marked as reviewed.');
    }

    private function blankLessonCategories(): array
    {
        return [
            'finger_exercise' => [],
            'theory_and_application' => [],
            'guided_practice' => [],
            'repertoire' => [],
        ];
    }

    public function editPlan(User $user)
    {
        $plan = PersonalizedPlan::where('user_id', $user->id)->first();

        $months = $plan?->months ?? [];
        while (count($months) < 3) {
            $months[] = ['lessons' => $this->blankLessonCategories()];
        }

        $initialPlan = [
            'skill_level' => $plan?->skill_level ?? '',
            'goal' => $plan?->goal ?? '',
            'start_date' => $plan?->start_date?->toDateString() ?? '',
            'ninety_day_target' => $plan?->ninety_day_target ?: [''],
            'months' => $months,
        ];

        $copyOptions = PersonalizedPlan::with('user')
            ->where('user_id', '!=', $user->id)
            ->get()
            ->filter(fn ($p) => $p->user)
            ->map(fn ($p) => [
                'id' => $p->user_id,
                'name' => $p->user->full_name ?? $p->user->name ?? $p->user->email,
                'email' => $p->user->email,
            ])
            ->values();

        return view('admin.personalized_guidance.plan', [
            'user' => $user,
            'initialPlan' => $initialPlan,
            'copyOptions' => $copyOptions,
        ]);
    }

    public function planCopyData(User $sourceUser)
    {
        $plan = PersonalizedPlan::where('user_id', $sourceUser->id)->firstOrFail();

        $months = $plan->months ?? [];
        while (count($months) < 3) {
            $months[] = ['lessons' => $this->blankLessonCategories()];
        }

        return response()->json([
            'skill_level' => $plan->skill_level ?? '',
            'goal' => $plan->goal ?? '',
            'ninety_day_target' => $plan->ninety_day_target ?: [''],
            'months' => $months,
        ]);
    }

    public function updatePlan(Request $request, User $user)
    {
        $validated = $request->validate([
            'skill_level' => 'nullable|string|in:Early Beginner,Advanced Beginner,Intermediate,Upper Intermediate,Advanced',
            'goal' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'ninety_day_target' => 'nullable|string',
            'months' => 'nullable|string',
        ]);

        $ninetyDayTarget = collect(json_decode($validated['ninety_day_target'] ?? '[]', true))
            ->map(fn ($item) => trim((string) $item))
            ->filter(fn ($item) => $item !== '')
            ->values()
            ->all();

        $categoryKeys = ['finger_exercise', 'theory_and_application', 'guided_practice', 'repertoire'];

        $months = collect(json_decode($validated['months'] ?? '[]', true))
            ->map(function ($month) use ($categoryKeys) {
                $lessons = [];
                foreach ($categoryKeys as $key) {
                    $lessons[$key] = collect($month['lessons'][$key] ?? [])
                        ->map(fn ($item) => [
                            'id' => ($item['id'] ?? '') ?: (string) \Illuminate\Support\Str::uuid(),
                            'name' => trim((string) ($item['name'] ?? '')),
                            'url' => trim((string) ($item['url'] ?? '')),
                            'week' => trim((string) ($item['week'] ?? '')),
                            'duration' => trim((string) ($item['duration'] ?? '')),
                        ])
                        ->filter(fn ($item) => $item['name'] !== '' || $item['url'] !== '')
                        ->values()
                        ->all();
                }

                return [
                    'lessons' => $lessons,
                ];
            })
            ->all();

        PersonalizedPlan::updateOrCreate(
            ['user_id' => $user->id],
            [
                'skill_level' => $validated['skill_level'] ?? null,
                'goal' => $validated['goal'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'ninety_day_target' => $ninetyDayTarget,
                'months' => $months,
            ]
        );

        if ($user->email) {
            try {
                Mail::to($user->email)->send(new PersonalizedGuidanceReady($user));
            } catch (\Exception $e) {
                Log::warning('Failed to email member that personalized guidance is ready: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.personalized-guidance.show', $user)->with('success', 'Personalized plan saved.');
    }
}
