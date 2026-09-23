<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalizedGuidanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'youtube_link',
        'chord_vocabulary',
        'key_fluency',
        'inspiration_pianist',
        'archetype',
        'practice_days_per_week',
        'practice_time_per_day',
        'playing_by_ear',
        'experience_level',
        'style_focus',
        'primary_goal',
        'details',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
