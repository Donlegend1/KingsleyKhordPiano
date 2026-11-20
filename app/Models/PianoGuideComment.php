<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PianoGuideComment extends Model
{
    protected $fillable = ['guide_slug', 'user_id', 'parent_id', 'body'];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')->whereNotNull('body')->orderBy('id');
    }
}
