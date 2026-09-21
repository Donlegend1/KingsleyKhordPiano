<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtudeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'position',
        'thumbnail',
    ];

    protected $appends = ['thumbnail_url'];

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset($this->thumbnail) : null;
    }

    public function etudes()
    {
        return $this->hasMany(Etude::class, 'etude_category_id')->orderByRaw('position IS NULL, position ASC');
    }
}
