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
        'details',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
