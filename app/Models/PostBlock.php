<?php

namespace App\Models;

use App\Helpers\VideoHelper;
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

    public function getEmbedUrlAttribute($value)
    {
        if ($this->type === 'link' && $this->content) {
            return VideoHelper::linkToEmbed($this->content);
        }

        return $value;
    }
}
