<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicalApplicationCategory extends Model
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

    public function lessons()
    {
        return $this->hasMany(MusicalApplication::class, 'musical_application_category_id')->orderBy('position');
    }
}
