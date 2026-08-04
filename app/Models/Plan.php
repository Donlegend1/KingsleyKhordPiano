<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $casts = [
        'paypal_plan_ids' => 'array',
        'price_ngn' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'price_eur' => 'decimal:2',
    ];

    protected $fillable = [
        'tier',
        'type',
        'price_ngn',
        'price_usd',
        'price_eur',
        'image',
        'background',
        'paystack_product_id',
        'stripe_product_id',
        'paypal_product_id',
        'paypal_plan_ids',
        'agent',
        'product_id',
        'price_id',
    ];
}
