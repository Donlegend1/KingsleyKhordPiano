<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liveshow extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'start_time', 'access_type', 'zoom_link', 'recording_url', 'category', 'max_slots'];

    public function bookedUsers()
    {
        return $this->belongsToMany(User::class, 'liveshow_bookings', 'liveshow_id', 'user_id')
                    ->select(['users.id', 'users.first_name', 'users.last_name', 'users.passport'])
                    ->withTimestamps();
    }
}
