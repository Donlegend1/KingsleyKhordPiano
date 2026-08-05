<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCheckpoint extends Model
{
    protected $fillable = [
        'course_category_id',
        'checkpoint_key',
        'title',
        'description',
        'linked_course_id',
        'redirect_url',
        'position',
    ];

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function linkedCourse()
    {
        return $this->belongsTo(Course::class, 'linked_course_id');
    }
}
