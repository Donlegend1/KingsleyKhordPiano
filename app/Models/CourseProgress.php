<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'course_id',
        'course_category',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
