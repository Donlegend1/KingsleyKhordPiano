<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnSongCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'level',
        'position',
    ];

    public function songs()
    {
        return $this->hasMany(LearnSong::class, 'learn_song_category_id')->orderBy('position');
    }
}
