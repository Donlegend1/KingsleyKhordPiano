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
        'includes_coaching' => 'boolean',
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
        'slug',
        'includes_coaching',
    ];

    public function isPremium(): bool
    {
        return stripos((string) $this->tier, 'premium') !== false;
    }

    /**
     * Piano coaching is for legacy Premium only. The new $45 / €39 Premium
     * (and its quarterly/yearly counterparts) does not include it.
     */
    public function includesCoaching(): bool
    {
        if (! $this->isPremium() || $this->isNewPremiumWithoutCoaching()) {
            return false;
        }

        if ($this->getAttribute('includes_coaching') !== null) {
            return (bool) $this->includes_coaching;
        }

        return true;
    }

    public function isNewPremiumWithoutCoaching(): bool
    {
        if (! $this->isPremium()) {
            return false;
        }

        $usd = (float) $this->price_usd;
        $eur = (float) $this->price_eur;

        return (abs($usd - 45.00) < 0.05 && abs($eur - 39.00) < 0.05)
            || (abs($usd - 120.00) < 0.05 && abs($eur - 105.00) < 0.05)
            || (abs($usd - 380.00) < 0.05 && abs($eur - 327.00) < 0.05);
    }
}
