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
        'thumbnail_youtube_id',
        'thumbnail_path',
        'overview',
        'linked_course_id',
        'redirect_url',
        'position',
    ];

    protected $appends = ['video_embed_url', 'thumbnail_url'];

    public function getVideoEmbedUrlAttribute()
    {
        return !$this->thumbnail_url && $this->video_url ? \App\Helpers\VideoHelper::linkToEmbed($this->video_url) : null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail_path) {
            return '/storage/' . $this->thumbnail_path;
        }

        $id = $this->thumbnail_youtube_id;

        return $id ? "https://i.ytimg.com/vi/{$id}/hqdefault.jpg" : null;
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
