<?php

namespace App\Support;

use App\Models\ShopProduct;

class ShopCatalog
{
    /**
     * Product catalog for the shop feature, backed by the shop_products table.
     * Keyed by slug so cart/checkout can hydrate full product details from just
     * a slug stored in the session.
     */
    public static function all(): array
    {
        return ShopProduct::all()
            ->mapWithKeys(fn (ShopProduct $product) => [$product->slug => static::toArray($product)])
            ->all();
    }

    public static function find(string $slug): ?array
    {
        $product = ShopProduct::where('slug', $slug)->first();

        return $product ? static::toArray($product) : null;
    }

    protected static function toArray(ShopProduct $product): array
    {
        return [
            'slug' => $product->slug,
            'name' => $product->title,
            'price' => $product->price,
            'original_price' => $product->original_price,
            'type' => $product->type,
            'label' => strtoupper($product->title),
            'thumbnail' => $product->thumbnail,
            'download_url' => $product->download_url,
            'video_url' => $product->video_url,
            'system_requirements' => $product->system_requirements,
            'from' => $product->gradient_from,
            'to' => $product->gradient_to,
        ];
    }
}
