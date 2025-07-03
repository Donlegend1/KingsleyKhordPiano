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
        // 'image_path',
        'price',
        'duration',
        'level',
        'status',
        // 'prerequisites',
        // 'what_you_will_learn',
        // 'resources',
        'requirements',

    ];

    public function completedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_video_completions')->withTimestamps();
    }

    public function progress(){
        return $this->hasOne(CourseProgress::class);
    }
}
