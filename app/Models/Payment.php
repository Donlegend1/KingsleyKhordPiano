<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

     protected $casts = [
    'metadata' => 'array',
    'starts_at' => 'datetime',
    'ends_at' => 'datetime',
    'notify_at' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'notified_at' => 'datetime',
  ];

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'status',
        'ends_at',
        'notified_at',
        'metadata',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $dates = [
        'starts_at',
        'ends_at',
        'notify_at',
        'created_at',
        'updated_at',
        'notified_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

