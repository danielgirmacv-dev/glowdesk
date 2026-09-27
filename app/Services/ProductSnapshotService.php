<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ProductSnapshotService
{
    /**
     * Directory path where product HTML snapshots are stored.
     */
    protected function getStorageDirectory(): string
    {
        return storage_path('app/snapshots/products');
    }

    /**
     * Get the absolute path to a product's static HTML snapshot.
     */
    public function getSnapshotPath(Product|int $product): string
    {
        $id = $product instanceof Product ? $product->id : $product;
        return $this->getStorageDirectory() . DIRECTORY_SEPARATOR . $id . '.html';
    }

    /**
     * Determine if a static snapshot exists on disk.
     */
    public function hasSnapshot(Product|int $product): bool
    {
        $path = $this->getSnapshotPath($product);
        return File::exists($path) && File::size($path) > 0;
    }

    /**
     * Retrieve the cached static HTML content if it exists.
     */
    public function getSnapshot(Product|int $product): ?string
    {
        $path = $this->getSnapshotPath($product);
        if (File::exists($path)) {
            try {
                return File::get($path);
            } catch (\Throwable $e) {
                Log::warning("Could not read snapshot at {$path}: " . $e->getMessage());
            }
        }
        return null;
    }

    /**
     * Save HTML snapshot content to disk.
     */
    public function saveSnapshot(Product|int $product, string $html): void
    {
        $dir = $this->getStorageDirectory();
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $path = $this->getSnapshotPath($product);
        File::put($path, $html);
    }

    /**
     * Render the product's Blade view into full HTML string.
     */
    public function renderSnapshot(Product $product): string
    {
        // Eager load related products for recommendations
        $relatedProducts = Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('shop.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ])->render();
    }

    /**
     * Generate, write to disk, and return the product's HTML snapshot.
     */
    public function generate(Product $product): string
    {
        $html = $this->renderSnapshot($product);
        $this->saveSnapshot($product, $html);
        return $html;
    }

    /**
     * Delete a product's static snapshot from disk.
     */
    public function delete(Product|int $product): bool
    {
        $path = $this->getSnapshotPath($product);
        if (File::exists($path)) {
            return File::delete($path);
        }
        return false;
    }

    /**
     * Clear all product snapshots from disk.
     */
    public function clearAll(): int
    {
        $dir = $this->getStorageDirectory();
        if (!File::isDirectory($dir)) {
            return 0;
        }

        $files = File::files($dir);
        $count = count($files);
        File::cleanDirectory($dir);
        return $count;
    }

    /**
     * Regenerate snapshots for all active products.
     */
    public function generateAll(): int
    {
        $products = Product::where('is_active', true)->get();
        $count = 0;

        foreach ($products as $product) {
            try {
                $this->generate($product);
                $count++;
            } catch (\Throwable $e) {
                Log::error("Failed generating snapshot for Product #{$product->id}: " . $e->getMessage());
            }
        }

        return $count;
    }
}
