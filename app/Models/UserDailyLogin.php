<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDailyLogin extends Model
{
    protected $fillable = [
        'user_id',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record that the given user was active today, in their own timezone (idempotent).
     */
    public static function recordToday(int $userId, ?string $timezone = null): void
    {
        static::firstOrCreate([
            'user_id' => $userId,
            'date' => now($timezone ?: config('app.timezone'))->toDateString(),
        ]);
    }

    /**
     * 'active'/'inactive'/'neutral' per weekday (0=Monday..6=Sunday) for the
     * current week, in the user's own timezone. Mirrors the inline logic in
     * components/memberarea/stats.blade.php so both widgets stay in sync.
     */
    public static function thisWeekStatuses(int $userId, ?string $timezone = null): array
    {
        $timezone = $timezone ?: config('app.timezone');
        $today = now($timezone)->startOfDay();
        $weekStart = $today->copy()->startOfWeek();
        $registeredOn = User::find($userId)?->created_at?->copy()->setTimezone($timezone)->startOfDay();

        $loginDates = static::where('user_id', $userId)
            ->where('date', '>=', $weekStart->toDateString())
            ->pluck('date')
            ->map(fn ($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->toArray();

        $statuses = [];
        foreach (range(0, 6) as $i) {
            $date = $weekStart->copy()->addDays($i);
            if ($date->gt($today) || ($registeredOn && $date->lt($registeredOn))) {
                $statuses[$i] = 'neutral';
            } elseif (in_array($date->toDateString(), $loginDates, true)) {
                $statuses[$i] = 'active';
            } else {
                $statuses[$i] = 'inactive';
            }
        }

        return $statuses;
    }

    /**
     * Consecutive-day streak counting backwards from today, in the user's
     * own timezone. Mirrors components/memberarea/stats.blade.php.
     */
    public static function currentStreak(int $userId, ?string $timezone = null): int
    {
        $timezone = $timezone ?: config('app.timezone');
        $today = now($timezone)->startOfDay();
        $registeredOn = User::find($userId)?->created_at?->copy()->setTimezone($timezone)->startOfDay();

        $streak = 0;
        $cursor = $today->copy();
        while (
            (! $registeredOn || $cursor->gte($registeredOn))
            && static::where('user_id', $userId)->whereDate('date', $cursor->toDateString())->exists()
        ) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }
}
