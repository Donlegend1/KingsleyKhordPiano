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
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    protected $appends = ['thumbnail_url', 'category'];

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset($this->thumbnail) : null;
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
