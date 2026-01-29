<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'audio_file',
        'duration',
        'file_size',
    ];
}
