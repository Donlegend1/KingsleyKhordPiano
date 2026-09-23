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
        'related_lessons',
        'images',
        'audio_resource',
        'pdf_resource',
        'midi_resource',
        'position',
        'musical_application_category_id',
    ];

    protected $casts = [
        'related_lessons' => 'array',
        'images' => 'array',
    ];

    protected $appends = ['thumbnail_url', 'category', 'image_urls', 'audio_resource_url', 'pdf_resource_url', 'midi_resource_url'];

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return asset($this->thumbnail);
        }

        return $this->applicationCategory?->thumbnail_url;
    }

    public function getImageUrlsAttribute()
    {
        if (!$this->images) {
            return [];
        }
        return array_map(fn($path) => asset($path), $this->images);
    }

    public function getAudioResourceUrlAttribute()
    {
        return $this->audio_resource ? asset($this->audio_resource) : null;
    }

    public function getPdfResourceUrlAttribute()
    {
        return $this->pdf_resource ? asset($this->pdf_resource) : null;
    }

    public function getMidiResourceUrlAttribute()
    {
        return $this->midi_resource ? asset($this->midi_resource) : null;
    }

    public function getCategoryAttribute()
    {
        return 'musical application';
    }

    public function applicationCategory()
    {
        return $this->belongsTo(MusicalApplicationCategory::class, 'musical_application_category_id');
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
}
