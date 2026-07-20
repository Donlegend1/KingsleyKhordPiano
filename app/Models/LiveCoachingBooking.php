<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveCoachingBooking extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'time',
        'zoom_join_url',
        'zoom_meeting_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
