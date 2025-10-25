<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'level',
        'position',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'course_category_id');
    }
}
