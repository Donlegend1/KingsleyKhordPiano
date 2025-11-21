<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;
    protected $fillable = ['name','is_premium'];
    public function messages() {
        return $this->hasMany(ChatMessage::class, "room_id");
    }
}
