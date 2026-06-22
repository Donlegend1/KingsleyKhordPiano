<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'category',
        'description',
        'video_url',
        'level',
        'status',
        'skill_level',
        'thumbnail',
        'tags',
        'video_type',
        'series',
        'images',
    ];

    protected $casts = [
        'category' => 'string',
        'status' => 'string',
        'tags' => 'array',
        'images' => 'array',
    ];

    protected $appends = ['thumbnail_url', 'image_urls'];
    

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset($this->thumbnail) : null;
    }

    public function getImageUrlsAttribute()
    {
        if (!$this->images) {
            return [];
        }
        return array_map(fn($path) => asset($path), $this->images);
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
}
