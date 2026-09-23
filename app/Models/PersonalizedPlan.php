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
}
