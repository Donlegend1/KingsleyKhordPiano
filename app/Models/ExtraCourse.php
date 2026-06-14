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
    ];

    protected $casts = [
        'related_courses' => 'array',
    ];

    protected $appends = ['thumbnail_url'];

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset($this->thumbnail) : null;
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
