<?php

namespace App\Services;

use App\Models\Bookmark;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BookmarkService
{
    /**
     * Toggle a bookmark for the authenticated user.
     * Adds if it doesn't exist, removes if it exists.
     */
    public function toggle(Model $bookmarkable): bool
    {
        $user = Auth::user();

        $bookmark = Bookmark::where([
            'user_id' => $user->id,
            'bookmarkable_id' => $bookmarkable->id,
            'bookmarkable_type' => get_class($bookmarkable),
        ])->first();

        if ($bookmark) {
            // Already bookmarked → remove
            $bookmark->delete();
            return false; // Indicates bookmark removed
        }

        // Not bookmarked → create
        Bookmark::create([
            'user_id' => $user->id,
            'bookmarkable_id' => $bookmarkable->id,
            'bookmarkable_type' => get_class($bookmarkable),
        ]);

        return true; // Indicates bookmark added
    }

    /**
     * Get all bookmarks of the authenticated user
     */
    public function getUserBookmarks()
    {
        return Auth::user()
            ->bookmarks()
            ->with('bookmarkable')
            ->latest()
            ->get();
    }

    /**
     * Check if a specific model is bookmarked by the authenticated user
     */
    public function isBookmarked(Model $bookmarkable): bool
    {
        // logger()->info([''=>$bookmarkable]);
        $user = Auth::user();

        return Bookmark::where([
            'user_id' => $user->id,
            'bookmarkable_id' => $bookmarkable->id,
            'bookmarkable_type' => get_class($bookmarkable),
        ])->exists();
    }

    /**
     * Count total bookmarks for a specific model
     */
    public function countBookmarks(Model $bookmarkable): int
    {
        return Bookmark::where([
            'bookmarkable_id' => $bookmarkable->id,
            'bookmarkable_type' => get_class($bookmarkable),
        ])->count();
    }
}
