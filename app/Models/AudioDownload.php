<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist',
        'collection',
        'category',
        'audio_file',
        'duration',
        'file_size',
    ];

    protected $appends = ['is_favorited'];

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function getIsFavoritedAttribute(): bool
    {
        $userId = auth()->id();

        if (!$userId) {
            return false;
        }

        return $this->bookmarks->contains('user_id', $userId);
    }
}
