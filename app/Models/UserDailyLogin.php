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
}
