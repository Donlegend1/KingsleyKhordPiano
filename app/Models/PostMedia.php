<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    use HasFactory;

    protected $fillable =[
        'post_id',
        'file_path',
        'premium_post_id',
        'type'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function premiumMessage()
    {
        return $this->belongsTo(ChatMessage::class, 'premium_post_id');
    }
}
