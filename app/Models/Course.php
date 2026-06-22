<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'description',
        'video_url',
        'thumbnail',
        'price',
        'duration',
        'video_type',
        'level',
        'status',
        'course_category_id',
        'requirements',
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

    public function completedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_video_completions')->withTimestamps();
    }

    public function progress(){
        return $this->hasOne(CourseProgress::class);
    }

    public function categoryPosition()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
}
