<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorialComment extends Model
{
    protected $fillable = [
        'tutorial_id',
        'user_id',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tutorial()
    {
        return $this->belongsTo(Tutorial::class);
    }
}
