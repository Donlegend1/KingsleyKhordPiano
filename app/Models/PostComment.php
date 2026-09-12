<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    
    protected $fillable =[
    'post_id',
    'user_id',
    'body'
    ];

    protected $appends = ['liked_by_user', 'likes_count'];

    public function getLikedByUserAttribute(): bool
    {
        $userId = auth()->id();

        if (!$userId) {
            return false;
        }

        return $this->likes->contains('user_id', $userId);
    }

    public function getLikesCountAttribute(): int
    {
        return $this->likes->count();
    }

    /**
     * Get the user associated with the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(PostReply::class, 'comment_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

}
