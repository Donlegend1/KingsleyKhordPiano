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
        'video_url',
        'overview',
        'linked_course_id',
        'redirect_url',
        'position',
    ];

    protected $appends = ['video_embed_url'];

    public function getVideoEmbedUrlAttribute()
    {
        return $this->video_url ? \App\Helpers\VideoHelper::linkToEmbed($this->video_url) : null;
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function linkedCourse()
    {
        return $this->belongsTo(Course::class, 'linked_course_id');
    }

    public function downloads()
    {
        return $this->hasMany(CourseCheckpointDownload::class)->orderBy('position');
    }
}
