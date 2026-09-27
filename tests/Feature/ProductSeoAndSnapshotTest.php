<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\ProductSnapshotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProductSeoAndSnapshotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear snapshots before tests
        app(ProductSnapshotService::class)->clearAll();
    }

    protected function tearDown(): void
    {
        // Clean up snapshots after tests
        app(ProductSnapshotService::class)->clearAll();
        parent::tearDown();
    }

    public function test_homepage_serves_ssr_product_grid_in_initial_html(): void
    {
        $product = Product::create([
            'name' => 'SEO Hydrating Serum Test',
            'description' => 'A deeply hydrating facial serum with hyaluronic acid.',
            'price' => 1250.00,
            'image_url' => 'https://example.com/serum.jpg',
            'is_active' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Verify initial HTML source contains real SSR content (not empty div or template only)
        $response->assertSee('id="ssr-product-grid"', false);
        $response->assertSee('SEO Hydrating Serum Test');
        $response->assertSee('1,250.00');
        $response->assertSee($product->url);

        // Verify homepage OpenGraph tags
        $response->assertSee('property="og:site_name"', false);
        $response->assertSee('content="GlowAddis"', false);
    }

    public function test_product_detail_page_serves_full_html_and_meta_tags(): void
    {
        $product = Product::create([
            'name' => 'Glow Vitamin C Radiance Cream',
            'description' => 'Brightening moisturizer for natural radiant glow.',
            'price' => 890.50,
            'image_url' => 'https://example.com/vitamin-c.jpg',
            'is_active' => true,
        ]);

        $url = route('shop.product', ['product' => $product->id, 'slug' => $product->slug]);
        $response = $this->get($url);

        $response->assertStatus(200);

        // Check semantic HTML
        $response->assertSee('<h1', false);
        $response->assertSee('Glow Vitamin C Radiance Cream');
        $response->assertSee('890.50');
        $response->assertSee('Brightening moisturizer for natural radiant glow.');

        // Check OpenGraph tags
        $response->assertSee('<meta property="og:title" content="Glow Vitamin C Radiance Cream – GlowAddis">', false);
        $response->assertSee('<meta property="og:type" content="product">', false);
        $response->assertSee('<meta property="og:image" content="https://example.com/vitamin-c.jpg">', false);
        $response->assertSee('<meta property="product:price:amount" content="890.5">', false);
        $response->assertSee('<meta property="product:price:currency" content="ETB">', false);

        // Check Twitter card
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);

        // Check Schema.org JSON-LD
        $response->assertSee('"@type": "Product"', false);
        $response->assertSee('"price": "890.50"', false);
        $response->assertSee('"priceCurrency": "ETB"', false);
    }

    public function test_non_canonical_slug_redirects_with_301(): void
    {
        $product = Product::create([
            'name' => 'Cerave Hydrating Cleanser',
            'description' => 'Gentle foaming cleanser for dry skin.',
            'price' => 950.00,
            'is_active' => true,
        ]);

        // Request without slug
        $response = $this->get('/products/' . $product->id);
        $response->assertStatus(301);
        $response->assertRedirect($product->url);

        // Request with wrong slug
        $responseWrong = $this->get('/products/' . $product->id . '/wrong-slug');
        $responseWrong->assertStatus(301);
        $responseWrong->assertRedirect($product->url);
    }

    public function test_snapshots_are_saved_to_disk_and_served_as_cache_hit(): void
    {
        $product = Product::create([
            'name' => 'Snapshotted Night Cream',
            'description' => 'Intense night recovery treatment.',
            'price' => 1400.00,
            'is_active' => true,
        ]);

        $snapshotService = app(ProductSnapshotService::class);
        $path = $snapshotService->getSnapshotPath($product);

        // Auto-generation on save should have created the file
        $this->assertFileExists($path);

        // First request receives HIT because snapshot was pre-generated on model save
        $response = $this->get($product->url);
        $response->assertStatus(200);
        $response->assertHeader('X-Snapshot-Cache', 'HIT');

        // Delete the snapshot manually to test on-demand MISS -> generation
        $snapshotService->delete($product);
        $this->assertFileDoesNotExist($path);

        $responseMiss = $this->get($product->url);
        $responseMiss->assertStatus(200);
        $responseMiss->assertHeader('X-Snapshot-Cache', 'MISS');
        $this->assertFileExists($path);

        // Second request should be a HIT
        $responseHit = $this->get($product->url);
        $responseHit->assertStatus(200);
        $responseHit->assertHeader('X-Snapshot-Cache', 'HIT');
    }

    public function test_artisan_command_generates_and_clears_snapshots(): void
    {
        Product::create([
            'name' => 'Artisan Test Item 1',
            'description' => 'Test Item 1 description.',
            'price' => 500.00,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Artisan Test Item 2',
            'description' => 'Test Item 2 description.',
            'price' => 600.00,
            'is_active' => true,
        ]);

        // Run clear
        $this->artisan('products:generate-snapshots --clear')
            ->expectsOutputToContain('Clearing all product snapshots...')
            ->assertExitCode(0);

        $snapshotService = app(ProductSnapshotService::class);
        $this->assertEquals(0, count(File::files(storage_path('app/snapshots/products'))));

        // Run generate
        $this->artisan('products:generate-snapshots')
            ->expectsOutputToContain('Generating static HTML snapshots for active products...')
            ->assertExitCode(0);

        $this->assertGreaterThanOrEqual(2, count(File::files(storage_path('app/snapshots/products'))));
    }

    public function test_sitemap_xml_lists_active_products(): void
    {
        $product = Product::create([
            'name' => 'Sitemap Skincare Solution',
            'description' => 'Special sitemap test product.',
            'price' => 775.00,
            'is_active' => true,
        ]);

        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<urlset', false);
        $response->assertSee(url('/'));
        $response->assertSee($product->url);
    }
}
