<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'category',
        'description',
        'video_url',
        'level',
        'status',
        'thumbnail',
    ];

    protected $casts = [
        'category' => 'string',
        'status' => 'string',
    ];

    protected $appends = ['thumbnail_url'];
    

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

 
}
