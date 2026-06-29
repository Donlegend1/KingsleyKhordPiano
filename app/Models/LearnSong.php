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
        'images',
        'audio_resource',
        'pdf_resource',
    ];

    protected $casts = [
        'related_songs' => 'array',
        'images' => 'array',
    ];

    protected $appends = ['thumbnail_url', 'image_urls', 'audio_resource_url', 'pdf_resource_url'];

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset($this->thumbnail) : null;
    }

    public function getAudioResourceUrlAttribute()
    {
        return $this->audio_resource ? asset($this->audio_resource) : null;
    }

    public function getPdfResourceUrlAttribute()
    {
        return $this->pdf_resource ? asset($this->pdf_resource) : null;
    }

    public function getImageUrlsAttribute()
    {
        if (!$this->images) {
            return [];
        }
        return array_map(fn($path) => asset($path), $this->images);
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
