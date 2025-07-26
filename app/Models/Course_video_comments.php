<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course_video_comments extends Model
{
    use HasFactory;
    protected $fillable = [
        'category',
        'course_id',
        'user_id',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Course_video_comment_replies::class, 'course_video_comment_id');
    }
}
