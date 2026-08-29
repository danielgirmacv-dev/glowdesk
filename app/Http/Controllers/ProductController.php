<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->get();
        return view('shop.index', compact('products'));
    }

    // Admin routes
    public function adminIndex()
    {
        $products = Product::orderBy('id', 'desc')->paginate(24);
        return view('admin.products', compact('products'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120'
        ]);

        $file = $request->file('csv_file');
        
        $count = 0;
        
        DB::beginTransaction();
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $header = true;
            foreach ($rows as $row) {
                if ($header) {
                    $header = false;
                    continue;
                }
                
                // Assuming format: Name, Description, Price, ImageURL (optional)
                if (count($row) >= 3) {
                    $name = trim($row[0] ?? '');
                    $desc = trim($row[1] ?? '');
                    $price = floatval(trim($row[2] ?? 0));
                    $imageUrl = isset($row[3]) ? trim($row[3]) : null;
                    if (empty($imageUrl)) $imageUrl = null;

                    if (!empty($name) && $price >= 0) {
                        Product::create([
                            'name' => $name,
                            'description' => $desc,
                            'price' => $price,
                            'image_url' => $imageUrl,
                            'is_active' => true
                        ]);
                        $count++;
                    }
                }
            }
            DB::commit();
            return redirect()->route('admin.products')->with('success', "Successfully imported $count products.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.products')->with('error', 'Error importing products: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.products_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|max:5120', // Add local image validation (5MB max)
        ]);

        if ($request->hasFile('image')) {
            $uploadedUrl = $this->handleImageUpload($request);
            if ($uploadedUrl) {
                $validated['image_url'] = $uploadedUrl;
            }
        }

        Product::create($validated);
        return redirect()->route('admin.products')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        return view('admin.products_edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:5120', // Add local image validation (5MB max)
        ]);

        if ($request->hasFile('image')) {
            $uploadedUrl = $this->handleImageUpload($request);
            if ($uploadedUrl) {
                $validated['image_url'] = $uploadedUrl;
            }
        }

        $product->update($validated);
        return redirect()->route('admin.products')->with('success', 'Product updated successfully!');
    }

    private function handleImageUpload(Request $request): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $uploadPreset = env('CLOUDINARY_UPLOAD_PRESET');
        $cloudinaryUrl = env('CLOUDINARY_URL');

        // Strategy 1: Cloudinary Unsigned Upload Preset (Easiest & Free)
        if ($cloudName && $uploadPreset) {
            try {
                $response = \Illuminate\Support\Facades\Http::attach(
                    'file',
                    file_get_contents($request->file('image')->getRealPath()),
                    $request->file('image')->getClientOriginalName()
                )->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                    'upload_preset' => $uploadPreset,
                ]);

                if ($response->successful()) {
                    return $response->json('secure_url');
                }
            } catch (\Exception $e) {
                // Fallback to local storage on error
            }
        }

        // Strategy 2: Cloudinary URL (cloudinary://API_KEY:API_SECRET@CLOUD_NAME)
        if ($cloudinaryUrl) {
            try {
                $parsed = parse_url($cloudinaryUrl);
                $cName = $parsed['host'] ?? null;
                $cKey = $parsed['user'] ?? null;
                $cSecret = $parsed['pass'] ?? null;
                if ($cName && $cKey && $cSecret) {
                    $timestamp = time();
                    $signature = sha1("timestamp={$timestamp}" . $cSecret);
                    $response = \Illuminate\Support\Facades\Http::attach(
                        'file',
                        file_get_contents($request->file('image')->getRealPath()),
                        $request->file('image')->getClientOriginalName()
                    )->post("https://api.cloudinary.com/v1_1/{$cName}/image/upload", [
                        'api_key' => $cKey,
                        'timestamp' => $timestamp,
                        'signature' => $signature,
                    ]);

                    if ($response->successful()) {
                        return $response->json('secure_url');
                    }
                }
            } catch (\Exception $e) {
                // Fallback to local storage on error
            }
        }

        // Fallback: Local storage
        $path = $request->file('image')->store('products', 'public');
        return '/storage/' . $path;
    }


    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
    }

    public function destroyAll()
    {
        $count = Product::count();
        Product::query()->delete();
        return response()->json(['success' => true, 'message' => "All $count products deleted."]);
    }
}
