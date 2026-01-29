<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostBlock extends Model
{
    use HasFactory;

     protected $fillable =[
        'post_id',
        'type',
        'content',
        'embed_url',
        'position'
    ];
}
