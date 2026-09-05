<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizReferenceAudio extends Model
{
    use HasFactory;

    protected $table = 'quiz_reference_audios';

    protected $fillable = ['quiz_id', 'name', 'audio_path'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
