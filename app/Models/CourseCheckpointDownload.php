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
        if (! $this->file_path) {
            return null;
        }

        // asset() doesn't URL-encode the path, so a stored filename with
        // spaces/special characters (from the original uploaded file name)
        // produces a URL the browser/server can't resolve, which shows up
        // as a 404 that Chrome mislabels as "File wasn't available on site"
        // and saves as .html. Encode each path segment while keeping slashes.
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $this->file_path)));

        return asset($encodedPath);
    }
}
