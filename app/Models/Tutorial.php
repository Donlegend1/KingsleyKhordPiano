<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'video_type',
        'thumbnail',
        'duration',
        'level',
        'author_name',
        'author_avatar',
        'status',
    ];

    protected $appends = ['thumbnail_url', 'author_avatar_url'];

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset($this->thumbnail) : null;
    }

    public function getAuthorAvatarUrlAttribute()
    {
        return $this->author_avatar ? asset($this->author_avatar) : null;
    }

    public function comments()
    {
        return $this->hasMany(TutorialComment::class)->latest();
    }
}
