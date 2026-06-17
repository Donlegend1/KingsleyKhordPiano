<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnSong extends Model
{
    use HasFactory;

    protected $fillable = [
        'learn_song_category_id',
        'title',
        'description',
        'video_type',
        'video_url',
        'thumbnail',
        'level',
        'status',
        'position',
        'related_songs',
    ];

    protected $casts = [
        'related_songs' => 'array',
    ];

    protected $appends = ['thumbnail_url'];

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset($this->thumbnail) : null;
    }

    public function category()
    {
        return $this->belongsTo(LearnSongCategory::class, 'learn_song_category_id');
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
}
