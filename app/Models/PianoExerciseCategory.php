<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PianoExerciseCategory extends Model
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
        return $this->hasMany(Upload::class, 'piano_exercise_category_id')->orderBy('position');
    }
}
