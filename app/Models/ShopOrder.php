<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopOrder extends Model
{
    protected $fillable = [
        'order_number',
        'checkout_reference',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'country',
        'payment_method',
        'payment_reference',
        'status',
        'total',
        'currency',
        'items',
        'paid_at',
    ];

    protected $casts = [
        'items' => 'array',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
