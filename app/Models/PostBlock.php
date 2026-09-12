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
        'original_name',
        'embed_url',
        'position'
    ];

    protected $appends = ['file_size'];

    public function getEmbedUrlAttribute($value)
    {
        if ($this->type === 'link' && $this->content) {
            return VideoHelper::linkToEmbed($this->content);
        }

        return $value;
    }

    public function getFileSizeAttribute(): ?int
    {
        if ($this->type !== 'file' || !$this->content) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($this->content)
            ? \Illuminate\Support\Facades\Storage::disk('public')->size($this->content)
            : null;
    }
}
