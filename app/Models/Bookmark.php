<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Upload;
use App\Models\Course;

class Bookmark extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'bookmarkable_id',
        'bookmarkable_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarkable()
    {
        return $this->morphTo();
    }

    // public function resolveVideo()
    // {
    //     switch ($this->source) {
    //         case 'uploads':
    //             return Upload::find($this->video_id);
    //         case 'courses':
    //             return Course::find($this->video_id);
    //         default:
    //             return null;
    //     }
    // }
}
