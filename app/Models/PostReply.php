<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostReply extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'comment_id',
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

    public function user()
{
    return $this->belongsTo(User::class);
}

    public function likes()
    {
        return $this->hasMany(ReplyLike::class);
    }
}
