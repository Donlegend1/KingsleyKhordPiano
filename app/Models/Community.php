<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $fillable = [
        'user_id', 'social', 'user_name', 'verified_status', 'bio', 'status'
    ];

    protected $casts = [
        'social' => 'array',
    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }
}
