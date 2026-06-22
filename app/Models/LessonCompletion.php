<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonCompletion extends Model
{
    protected $fillable = [
        'user_id',
        'completable_id',
        'completable_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function completable()
    {
        return $this->morphTo();
    }
}
