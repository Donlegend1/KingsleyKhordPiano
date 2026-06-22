<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'extra_course_category_id',
        'title',
        'description',
        'video_type',
        'video_url',
        'thumbnail',
        'level',
        'status',
        'position',
        'related_courses',
        'images',
    ];

    protected $casts = [
        'related_courses' => 'array',
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

    public function category()
    {
        return $this->belongsTo(ExtraCourseCategory::class, 'extra_course_category_id');
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
}
