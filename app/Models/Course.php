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

    ];

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
