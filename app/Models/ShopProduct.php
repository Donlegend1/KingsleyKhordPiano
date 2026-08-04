<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShopProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'slug',
        'thumbnail',
        'regular_price',
        'sale_price',
        'download_url',
        'video_url',
        'system_requirements',
        'gradient_from',
        'gradient_to',
    ];

    protected $casts = [
        'regular_price' => 'float',
        'sale_price' => 'float',
    ];

    protected static function booted()
    {
        static::saving(function (ShopProduct $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    /**
     * The price a customer actually pays: sale price if set, otherwise regular price.
     */
    public function getPriceAttribute(): float
    {
        return $this->sale_price ?? $this->regular_price;
    }

    /**
     * Only returns a value when the item is actually on sale (for showing a strikethrough price).
     */
    public function getOriginalPriceAttribute(): ?float
    {
        return $this->sale_price ? $this->regular_price : null;
    }
}
