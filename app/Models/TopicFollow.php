<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicFollow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subcategory',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
