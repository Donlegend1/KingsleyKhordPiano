<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MidiFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'video_path',
        'video_type',
        'midi_file_path',
        'lmv_file_path',
        'thumbnail_path',
        'description',
    ];
}
