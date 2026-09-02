<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'video_url', 'thumbnail_path', 'main_audio_path', 'category', 'difficulty', 'question_prompt', 'progression_numbers'];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function referenceAudios()
    {
        return $this->hasMany(QuizReferenceAudio::class)->orderBy('id');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path ? asset( $this->thumbnail_path) : null;
    }

}
