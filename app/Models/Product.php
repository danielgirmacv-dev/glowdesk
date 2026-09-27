<?php

namespace App\Models;

use App\Services\ProductSnapshotService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'is_active',
    ];

    protected $appends = [
        'slug',
        'url',
    ];

    /**
     * Model lifecycle hooks for automated snapshot generation & cache invalidation.
     */
    protected static function booted(): void
    {
        static::saved(function (Product $product) {
            try {
                if ($product->is_active) {
                    app(ProductSnapshotService::class)->generate($product);
                } else {
                    app(ProductSnapshotService::class)->delete($product);
                }
            } catch (\Throwable $e) {
                Log::warning("Could not auto-generate snapshot for Product #{$product->id}: " . $e->getMessage());
            }
        });

        static::deleted(function (Product $product) {
            try {
                app(ProductSnapshotService::class)->delete($product);
            } catch (\Throwable $e) {
                Log::warning("Could not delete snapshot for Product #{$product->id}: " . $e->getMessage());
            }
        });
    }

    /**
     * Generate an SEO-friendly URL slug from product name.
     */
    public function getSlugAttribute(): string
    {
        return Str::slug($this->name) ?: 'item-' . $this->id;
    }

    /**
     * Get the canonical public URL for this product.
     */
    public function getUrlAttribute(): string
    {
        return route('shop.product', [
            'product' => $this->id,
            'slug' => $this->slug,
        ]);
    }
}
