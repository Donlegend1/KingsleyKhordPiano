<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraCourseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'level',
        'position',
        'thumbnail',
    ];

    protected $appends = ['thumbnail_url'];

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset($this->thumbnail) : null;
    }

    public function courses()
    {
        return $this->hasMany(ExtraCourse::class, 'extra_course_category_id')->orderBy('position');
    }
}
