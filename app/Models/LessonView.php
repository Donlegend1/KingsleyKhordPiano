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

    /**
     * Whether any model in the given collection was created within the last
     * $sinceDays and has not yet been viewed by the user — used to drive the
     * "NEW" badge on course/category thumbnails and nav menu items.
     */
    public static function anyNewUnviewed($userId, $viewables, int $sinceDays = 7): bool
    {
        $viewables = collect($viewables)->filter();

        $newOnes = $viewables->filter(
            fn ($v) => $v->created_at && $v->created_at->gt(now()->subDays($sinceDays))
        );

        if (!$userId || $newOnes->isEmpty()) {
            return false;
        }

        $viewedIds = static::query()
            ->where('user_id', $userId)
            ->where('viewable_type', get_class($newOnes->first()))
            ->whereIn('viewable_id', $newOnes->pluck('id'))
            ->pluck('viewable_id');

        return $newOnes->pluck('id')->diff($viewedIds)->isNotEmpty();
    }
}
