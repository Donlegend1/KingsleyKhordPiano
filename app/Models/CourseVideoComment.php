<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseVideoComment extends Model
{
    use HasFactory;
    protected $fillable = [
        'category',
        'course_id',
        'user_id',
        'comment',
        'url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Upload::class);
    }

    public function replies()
    {
        return $this->hasMany(CourseVideoCommentReply::class, 'course_video_comment_id');
    }
}
