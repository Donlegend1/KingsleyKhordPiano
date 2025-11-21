<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;
     protected $fillable = ['room_id','user_id','parent_id','body','meta'];

    protected $casts = ['meta' => 'array'];

    public function room() { return $this->belongsTo(ChatRoom::class); }
    public function user() { return $this->belongsTo(User::class); }

    // parent message (if reply)
    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    // replies
    public function replies() { return $this->hasMany(self::class, 'parent_id'); }

    // likes
    public function likes() { return $this->morphMany(Like::class, 'likeable'); }

    public function likedBy(User $user) {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class, 'premium_post_id');
    }
}
