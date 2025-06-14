<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liveshow extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'start_time', 'access_type', 'zoom_link', 'recording_url'];
}
