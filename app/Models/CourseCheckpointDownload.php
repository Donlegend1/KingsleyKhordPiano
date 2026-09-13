<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCheckpointDownload extends Model
{
    protected $fillable = [
        'course_checkpoint_id',
        'title',
        'file_path',
        'position',
    ];

    protected $appends = ['file_url'];

    public function checkpoint()
    {
        return $this->belongsTo(CourseCheckpoint::class, 'course_checkpoint_id');
    }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset($this->file_path) : null;
    }
}
