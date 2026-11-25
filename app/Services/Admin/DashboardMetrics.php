<?php

namespace App\Services\Admin;

use App\Enums\Roles\UserRoles;
use App\Models\Course;
use App\Models\Etude;
use App\Models\ExtraCourse;
use App\Models\LearnSong;
use App\Models\MusicalApplication;
use App\Models\Upload;
use App\Models\User;
use App\Queries\UserSubscriptionSummary;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Real numbers for the admin dashboard. Every figure comes from data the app
 * already records; anything the app does not track is left to the view to
 * label as unavailable rather than estimated here.
 */
class DashboardMetrics
{
    public const LESSON_AREAS = [
        Course::class => 'Roadmap',
        Upload::class => 'Finger Exercises',
        Etude::class => 'Etudes & Pieces',
        MusicalApplication::class => 'Guided Practice',
        ExtraCourse::class => 'Extra Courses',
        LearnSong::class => 'Learn Songs',
    ];

    private ?Collection $countryNames = null;

    public function __construct(
        private readonly CarbonImmutable $from,
        private readonly CarbonImmutable $to,
    ) {
    }

    private function window(): array
    {
        return [$this->from->startOfDay(), $this->to->endOfDay()];
    }

    private function members()
    {
        return User::query()->where('role', UserRoles::MEMBER->value);
    }

    private function memberSummaries()
    {
        return DB::query()->fromSub(UserSubscriptionSummary::query(), 'summary')
            ->where('role', UserRoles::MEMBER->value);
    }

    /** Members whose plan gives them access today. */
    public function activeSubscribers(): int
    {
        return $this->memberSummaries()->where('display_subscription_status', 'Active')->count();
    }

    /** @return Collection<int, object{name: string, email: string, joined: CarbonImmutable, country: ?string, plan: string, status: string}> */
    public function latestSignups(int $limit = 10): Collection
    {
        $countries = $this->countryNames();

        return $this->memberSummaries()
            ->orderByDesc('created_at')->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name', 'email', 'country', 'metadata', 'created_at', 'display_plan', 'display_subscription_status'])
            ->map(fn ($user) => (object) [
                'id' => $user->id,
                'name' => trim($user->first_name . ' ' . $user->last_name) ?: '—',
                'email' => $user->email,
                'joined' => CarbonImmutable::parse($user->created_at),
                'country' => $this->countryLabel($this->signupCountry($user), $countries),
                'plan' => UserSubscriptionSummary::PLAN_LABELS[$user->display_plan] ?? 'No subscription',
                'status' => $user->display_subscription_status,
            ]);
    }

    /**
     * Members grouped by country, biggest first.
     *
     * @return array{rows: array<int, array{0: string, 1: int}>, other: int, notSet: int, total: int}
     */
    public function membersByCountry(bool $periodOnly = false, int $limit = 8): array
    {
        $query = $this->members()->select(['country', 'metadata']);
        if ($periodOnly) {
            $query->whereBetween('created_at', $this->window());
        }

        $counts = [];
        $notSet = 0;
        foreach ($query->cursor() as $user) {
            $code = $this->signupCountry($user);
            if ($code === null) {
                $notSet++;
                continue;
            }
            $counts[$code] = ($counts[$code] ?? 0) + 1;
        }
        arsort($counts);

        $names = $this->countryNames();
        $rows = [];
        foreach (array_slice($counts, 0, $limit, true) as $code => $count) {
            $rows[] = [$this->countryLabel($code, $names), $count];
        }

        return [
            'rows' => $rows,
            'other' => array_sum(array_slice($counts, $limit)),
            'notSet' => $notSet,
            'total' => array_sum($counts) + $notSet,
        ];
    }

    /**
     * What members who joined in the period did within seven days of joining.
     * Only members who joined at least seven days ago count, so everyone in
     * the group has had a full first week.
     *
     * @return array{cohort: int, steps: array<string, int>}
     */
    public function firstWeek(): array
    {
        $cutoff = CarbonImmutable::now()->subDays(7);
        $end = $this->to->endOfDay()->min($cutoff);
        $joined = $end->lt($this->from)
            ? collect()
            : $this->members()->whereBetween('created_at', [$this->from->startOfDay(), $end])->pluck('created_at', 'id');

        $steps = [
            'Created an account' => $joined->count(),
            'Took the assessment' => 0,
            'Opened a first lesson' => 0,
            'Completed a first lesson' => 0,
        ];
        if ($joined->isEmpty()) {
            return ['cohort' => 0, 'steps' => $steps];
        }

        $firsts = [
            'Took the assessment' => $this->firstActivity($joined->keys(), [['user_assessments', 'created_at']]),
            'Opened a first lesson' => $this->firstActivity($joined->keys(), [['lesson_views', 'created_at']]),
            'Completed a first lesson' => $this->firstActivity($joined->keys(), [['course_progress', 'created_at'], ['lesson_completions', 'created_at']]),
        ];
        foreach ($firsts as $step => $times) {
            foreach ($times as $userId => $at) {
                if (CarbonImmutable::parse($at)->lte(CarbonImmutable::parse($joined[$userId])->addDays(7))) {
                    $steps[$step]++;
                }
            }
        }

        return ['cohort' => $joined->count(), 'steps' => $steps];
    }

    /**
     * Of members who joined in the period and opened a lesson, how many came
     * back on a later day within 7 / 30 days of that first lesson. Each window
     * only counts students whose first lesson was at least that long ago.
     *
     * @return array<string, array{returned: int, eligible: int}>
     */
    public function returnToLearning(): array
    {
        $result = ['Within 7 days' => ['returned' => 0, 'eligible' => 0], 'Within 30 days' => ['returned' => 0, 'eligible' => 0]];
        $joined = $this->members()->whereBetween('created_at', $this->window())->pluck('id');
        $firstLesson = $this->firstActivity($joined, [['lesson_views', 'created_at']]);
        if ($firstLesson->isEmpty()) {
            return $result;
        }

        $visits = collect();
        foreach ($firstLesson->keys()->chunk(1000) as $ids) {
            $visits = $visits->concat(DB::table('user_daily_logins')->whereIn('user_id', $ids)->get(['user_id', 'date']));
        }
        $visitsByUser = $visits->groupBy('user_id');
        $today = CarbonImmutable::today();

        foreach ($firstLesson as $userId => $at) {
            $firstDay = CarbonImmutable::parse($at)->startOfDay();
            foreach (['Within 7 days' => 7, 'Within 30 days' => 30] as $label => $days) {
                if ($firstDay->addDays($days)->gt($today)) {
                    continue;
                }
                $result[$label]['eligible']++;
                $cameBack = ($visitsByUser[$userId] ?? collect())->contains(function ($visit) use ($firstDay, $days) {
                    $day = CarbonImmutable::parse($visit->date)->startOfDay();

                    return $day->gt($firstDay) && $day->lte($firstDay->addDays($days));
                });
                if ($cameBack) {
                    $result[$label]['returned']++;
                }
            }
        }

        return $result;
    }

    /**
     * Lessons opened by the most students for the first time in the period.
     * Lesson views are stored once per student and lesson, so repeat opens
     * by the same student are not counted.
     *
     * @return Collection<int, object{title: string, area: string, students: int}>
     */
    public function topLessons(int $limit = 10): Collection
    {
        $rows = DB::table('lesson_views')
            ->whereIn('viewable_type', array_keys(self::LESSON_AREAS))
            ->whereBetween('created_at', $this->window())
            ->selectRaw('viewable_type, viewable_id, COUNT(*) as students')
            ->groupBy('viewable_type', 'viewable_id')
            ->orderByDesc('students')->orderBy('viewable_type')->orderBy('viewable_id')
            ->limit($limit)
            ->get();

        $titles = [];
        foreach ($rows->groupBy('viewable_type') as $type => $group) {
            $titles[$type] = $type::query()->whereIn('id', $group->pluck('viewable_id'))->pluck('title', 'id');
        }

        return $rows->map(fn ($row) => (object) [
            'title' => $titles[$row->viewable_type][$row->viewable_id] ?? 'Deleted lesson',
            'area' => self::LESSON_AREAS[$row->viewable_type],
            'students' => (int) $row->students,
        ]);
    }

    /**
     * Subscription changes from the subscriptions table, which Stripe,
     * PayPal and Paystack all write to.
     */
    public function subscriptionActivity(): array
    {
        $now = CarbonImmutable::now();
        $subscriptions = DB::table('subscriptions')
            ->whereIn('user_id', $this->members()->select('id'));
        $endedWindow = [$this->from->startOfDay(), $this->to->endOfDay()->min($now)];
        $ended = (clone $subscriptions)->whereNotNull('ends_at')->whereBetween('ends_at', $endedWindow);

        return [
            'started' => (clone $subscriptions)->whereBetween('created_at', $this->window())->distinct()->count('user_id'),
            'endedCancelled' => (clone $ended)->whereIn('stripe_status', ['canceled', 'cancelled'])->distinct()->count('user_id'),
            'endedOther' => (clone $ended)->whereNotIn('stripe_status', ['canceled', 'cancelled'])->distinct()->count('user_id'),
            'cancelledWithAccess' => (clone $subscriptions)->whereIn('stripe_status', ['canceled', 'cancelled'])
                ->where('ends_at', '>', $now)->distinct()->count('user_id'),
            'pastDue' => (clone $subscriptions)->where('stripe_status', 'past_due')->distinct()->count('user_id'),
            'activeSubscribers' => $this->activeSubscribers(),
        ];
    }

    /**
     * Earliest time each user appears in any of the given tables.
     *
     * @param  iterable<int>  $userIds
     * @param  array<int, array{0: string, 1: string}>  $sources  [table, timestamp column]
     * @return Collection<int, string>  user id => earliest timestamp
     */
    private function firstActivity(iterable $userIds, array $sources): Collection
    {
        $earliest = [];
        foreach (collect($userIds)->chunk(1000) as $ids) {
            foreach ($sources as [$table, $column]) {
                $rows = DB::table($table)->whereIn('user_id', $ids)
                    ->groupBy('user_id')->selectRaw("user_id, MIN({$column}) as first_at")
                    ->pluck('first_at', 'user_id');
                foreach ($rows as $userId => $at) {
                    if ($at !== null && (! isset($earliest[$userId]) || $at < $earliest[$userId])) {
                        $earliest[$userId] = $at;
                    }
                }
            }
        }

        return collect($earliest);
    }

    private function signupCountry($user): ?string
    {
        $metadata = $user->metadata;
        if (is_string($metadata)) {
            $metadata = json_decode($metadata, true);
        }
        $country = trim((string) (data_get($metadata, 'signup_country') ?: $user->country));

        return $country === '' ? null : strtoupper($country);
    }

    private function countryNames(): Collection
    {
        return $this->countryNames ??= DB::table('countries')->pluck('country_name', 'country_code')
            ->mapWithKeys(fn ($name, $code) => [strtoupper($code) => $name]);
    }

    private function countryLabel(?string $code, Collection $names): ?string
    {
        if ($code === null) {
            return null;
        }

        return $names[$code] ?? ucwords(strtolower($code));
    }
}
