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
    ];

    public function etudes()
    {
        return $this->hasMany(Etude::class, 'etude_category_id')->orderByRaw('position IS NULL, position ASC');
    }
}
