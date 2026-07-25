<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryView extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Record that the given user has just visited a category listing
     * page (e.g. clicking "Extra Courses" in the nav), so the "NEW"
     * badge for that category clears.
     */
    public static function markViewed($userId, string $category): void
    {
        if (!$userId) {
            return;
        }

        static::updateOrCreate(
            ['user_id' => $userId, 'category' => $category],
            ['viewed_at' => now()]
        );
    }

    /**
     * When the user last viewed the given category's listing page,
     * or the beginning of time if they never have.
     */
    public static function lastViewedAt($userId, string $category)
    {
        if (!$userId) {
            return now()->subYears(50);
        }

        $viewedAt = static::where('user_id', $userId)
            ->where('category', $category)
            ->value('viewed_at');

        return $viewedAt ?: now()->subYears(50);
    }
}
