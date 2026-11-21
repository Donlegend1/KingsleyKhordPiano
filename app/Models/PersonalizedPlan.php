<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalizedPlan extends Model
{
    use HasFactory;

    public const PROGRAM_LENGTH_DAYS = 90;

    protected $fillable = [
        'user_id',
        'skill_level',
        'goal',
        'start_date',
        'ninety_day_target',
        'months',
        'completed_lessons',
    ];

    protected $casts = [
        'start_date' => 'date',
        'ninety_day_target' => 'array',
        'months' => 'array',
        'completed_lessons' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEndDateAttribute()
    {
        return $this->start_date?->copy()->addDays(self::PROGRAM_LENGTH_DAYS);
    }

    /**
     * The 90-day program splits into 3 equal 30-day months for display.
     */
    public function monthDateRange(int $monthIndex): ?array
    {
        if (! $this->start_date) {
            return null;
        }

        $start = $this->start_date->copy()->addDays($monthIndex * 30);
        $end = $start->copy()->addDays(29);

        return [$start, $end];
    }

    /**
     * Stable "monthIndex.categoryKey.lessonId" keys for every lesson in the
     * plan — used to track per-lesson completion since lessons live inside
     * the `months` JSON blob rather than as their own DB rows. Keyed by the
     * lesson's own `id` (not its array position) so completion status
     * survives an admin adding/removing/reordering other lessons later.
     */
    public function allLessonKeys(): array
    {
        $keys = [];
        foreach ($this->months ?? [] as $monthIndex => $month) {
            foreach ($month['lessons'] ?? [] as $categoryKey => $lessons) {
                foreach ($lessons as $lesson) {
                    if (! empty($lesson['id'])) {
                        $keys[] = "{$monthIndex}.{$categoryKey}.{$lesson['id']}";
                    }
                }
            }
        }

        return $keys;
    }

    public function lessonsByWeek(int $monthIndex): array
    {
        $weeks = [];
        $unscheduled = [];

        foreach ($this->months[$monthIndex]['lessons'] ?? [] as $category => $lessons) {
            foreach ($lessons as $lesson) {
                if (empty($lesson['id'])) {
                    continue;
                }

                $lesson['lesson_key'] = "{$monthIndex}.{$category}.{$lesson['id']}";
                $lesson['category_key'] = $category;
                $week = filter_var($lesson['week'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

                if ($week === false) {
                    $unscheduled[] = $lesson;
                } else {
                    $weeks[$week][] = $lesson;
                }
            }
        }

        ksort($weeks, SORT_NUMERIC);
        if ($unscheduled) {
            $weeks['unscheduled'] = $unscheduled;
        }

        return $weeks;
    }

    public function nextUnfinishedLesson(): ?array
    {
        $completed = array_fill_keys($this->completed_lessons ?? [], true);

        foreach (array_keys($this->months ?? []) as $monthIndex) {
            foreach ($this->lessonsByWeek($monthIndex) as $lessons) {
                foreach ($lessons as $lesson) {
                    if (!isset($completed[$lesson['lesson_key']])) {
                        return $lesson;
                    }
                }
            }
        }

        return null;
    }

    public function progressPercent(): int
    {
        $totalLessons = count($this->allLessonKeys());
        if ($totalLessons === 0) {
            return 0;
        }

        $completed = array_intersect($this->completed_lessons ?? [], $this->allLessonKeys());

        return (int) round((count($completed) / $totalLessons) * 100);
    }

    public function monthProgressPercent(int $monthIndex): int
    {
        $month = ($this->months ?? [])[$monthIndex] ?? null;
        if (! $month) {
            return 0;
        }

        $keys = [];
        foreach ($month['lessons'] ?? [] as $categoryKey => $lessons) {
            foreach ($lessons as $lesson) {
                if (! empty($lesson['id'])) {
                    $keys[] = "{$monthIndex}.{$categoryKey}.{$lesson['id']}";
                }
            }
        }

        if (count($keys) === 0) {
            return 0;
        }

        $completed = array_intersect($this->completed_lessons ?? [], $keys);

        return (int) round((count($completed) / count($keys)) * 100);
    }

    /**
     * Admin-facing snapshot of how a member is doing against their plan:
     * lessons done, overall %, per-month %, and pace vs the 90-day schedule.
     */
    public function progressSummary(): array
    {
        $keys = $this->allLessonKeys();
        $total = count($keys);
        $done = count(array_intersect($this->completed_lessons ?? [], $keys));
        $percent = $total > 0 ? (int) round(($done / $total) * 100) : 0;

        $started = $this->start_date && ! $this->start_date->isFuture();
        $elapsedDays = $started ? min(self::PROGRAM_LENGTH_DAYS, (int) $this->start_date->diffInDays(now())) : 0;
        $expected = (int) round(($elapsedDays / self::PROGRAM_LENGTH_DAYS) * 100);

        $pace = match (true) {
            $total === 0 => 'No lessons',
            $percent >= 100 => 'Completed',
            ! $started => 'Not started',
            $percent >= $expected + 10 => 'Ahead',
            $percent >= $expected - 10 => 'On track',
            default => 'Behind',
        };

        return [
            'done' => $done,
            'total' => $total,
            'percent' => $percent,
            'expected' => $expected,
            'day' => $elapsedDays,
            'started' => $started,
            'pace' => $pace,
            'months' => collect(array_keys($this->months ?? []))->map(fn ($m) => $this->monthProgressPercent((int) $m))->all(),
            'next' => $this->nextUnfinishedLesson()['name'] ?? null,
        ];
    }
}
