<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseVideoCommentReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_video_comment_id',
        'user_id',
        'reply',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
