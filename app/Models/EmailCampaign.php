<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'body',
        'targets',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'targets' => 'array',
        'sent_at' => 'datetime',
    ];
}
