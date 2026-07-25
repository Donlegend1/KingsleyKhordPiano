<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicalApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'video_url',
        'video_type',
        'skill_level',
        'series',
        'duration',
        'status',
        'tags',
        'audio_resource',
        'pdf_resource',
        'position',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    protected $appends = ['thumbnail_url', 'category', 'audio_resource_url', 'pdf_resource_url'];

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

    public function getCategoryAttribute()
    {
        return 'musical application';
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
}
