<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

   protected $fillable =[
    'title',
    'body',
    'category',
    'subcategory',
    'user_id',
    'parent_post_id',
    'is_pinned',
    'video_url'
   ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // The topic (e.g. a challenge post like "December challenge") this post
    // was submitted under, if any.
    public function parent()
    {
        return $this->belongsTo(Post::class, 'parent_post_id');
    }

    // Submissions made directly on this post's own topic page.
    public function submissions()
    {
        return $this->hasMany(Post::class, 'parent_post_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class);
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function blocks()
    {
        return $this->hasMany(PostBlock::class);
    }

    protected $appends = ['is_bookmarked', 'liked_by_user'];

    public function getIsBookmarkedAttribute(): bool
    {
        $userId = auth()->id();

        if (!$userId) {
            return false;
        }

        return $this->bookmarks->contains('user_id', $userId);
    }

    public function getLikedByUserAttribute(): bool
    {
        $userId = auth()->id();

        if (!$userId) {
            return false;
        }

        return $this->likes->contains('user_id', $userId);
    }
}
