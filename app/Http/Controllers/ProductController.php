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
            $path = $request->file('image')->store('products', 'public');
            $validated['image_url'] = '/storage/' . $path;
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
            $path = $request->file('image')->store('products', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $product->update($validated);
        return redirect()->route('admin.products')->with('success', 'Product updated successfully!');
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
