<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'amount_naria',
        'amount_dollar',
        'duration',
        'type',
        'status',
        'stripe_id',
        'stripe_status',
        'stripe_price',
        'quantity',
        'trial_ends_at',
        'ends_at',
        'notified_at',
        'plan_code',
        'subscription_code',
        'email_token',
        'authorization_code',
        'payment_method',
    ];

    public function item()
    {
        return $this->belongsTo(User::class);
    }
}
