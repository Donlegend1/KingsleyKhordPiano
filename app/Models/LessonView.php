<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonView extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'viewable_id',
        'viewable_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function viewable()
    {
        return $this->morphTo();
    }

    /**
     * Record (or refresh the timestamp of) a view for the given user/model.
     */
    public static function record($userId, $viewable): void
    {
        static::query()->updateOrCreate(
            [
                'user_id' => $userId,
                'viewable_id' => $viewable->getKey(),
                'viewable_type' => get_class($viewable),
            ],
            [
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Whether the given user has ever viewed this model — used to clear the
     * "NEW" badge once a lesson has actually been opened.
     */
    public static function hasViewed($userId, $viewable): bool
    {
        if (!$userId || !$viewable) {
            return false;
        }

        return static::query()
            ->where('user_id', $userId)
            ->where('viewable_id', $viewable->getKey())
            ->where('viewable_type', get_class($viewable))
            ->exists();
    }
}
