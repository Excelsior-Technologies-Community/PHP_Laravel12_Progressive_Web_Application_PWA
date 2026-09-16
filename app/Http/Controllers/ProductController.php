<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PRODUCT INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = $request->input('search');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        $allowedSorts = [
            'latest',
            'oldest',
            'name_asc',
            'name_desc',
            'price_low',
            'price_high',
        ];

        $sort = $request->input('sort', 'latest');

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'latest';
        }

        $query = Product::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', $maxPrice);
        }

        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(5)->withQueryString();

        $totalProducts = Product::count();
        $todayProducts = Product::whereDate('created_at', today())->count();
        $averagePrice = Product::avg('price') ?? 0;
        $highestPrice = Product::max('price') ?? 0;
        $lowestPrice = Product::min('price') ?? 0;

        return view(
            'product.index',
            compact(
                'products',
                'search',
                'minPrice',
                'maxPrice',
                'sort',
                'totalProducts',
                'todayProducts',
                'averagePrice',
                'highestPrice',
                'lowestPrice'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | API LIST (FOR PWA INDEXEDDB CACHE)
    |--------------------------------------------------------------------------
    */

    public function apiList(Request $request)
    {
        $products = Product::latest()->get();

        return response()->json([
            'status' => true,
            'total' => $products->count(),
            'products' => $products,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('product.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('products'), $imageName);
            $data['image'] = $imageName;
        }

        $product = Product::create($data);

        if ($request->wantsJson() || $request->ajax() || $request->header('X-PWA-Sync')) {
            return response()->json([
                'status' => true,
                'message' => 'Product created successfully.',
                'product' => $product,
            ], 201);
        }

        return redirect()
            ->route('product.index')
            ->with('success', 'Product created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Product $product)
    {
        return view('product.edit', compact('product'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteProductImage($product);
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('products'), $imageName);
            $data['image'] = $imageName;
        }

        $product->update($data);

        if ($request->wantsJson() || $request->ajax() || $request->header('X-PWA-Sync')) {
            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully.',
                'product' => $product,
            ]);
        }

        return redirect()
            ->route('product.index')
            ->with('success', 'Product updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE SINGLE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function destroy(Request $request, Product $product)
    {
        $this->deleteProductImage($product);
        $product->delete();

        if ($request->wantsJson() || $request->ajax() || $request->header('X-PWA-Sync')) {
            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully.',
            ]);
        }

        return redirect()
            ->route('product.index')
            ->with('success', 'Product deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | PWA OFFLINE BATCH SYNC ENDPOINT
    |--------------------------------------------------------------------------
    | Receives queued operations from IndexedDB and processes them atomically
    */

    public function sync(Request $request)
    {
        $request->validate([
            'queue' => 'required|array',
            'queue.*.action' => 'required|string|in:CREATE,UPDATE,DELETE',
            'queue.*.temp_id' => 'required',
        ]);

        $queue = $request->input('queue', []);
        $processedCount = 0;
        $mappings = [];
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($queue as $item) {
                $action = $item['action'] ?? '';
                $tempId = $item['temp_id'] ?? null;
                $payload = $item['data'] ?? [];

                if ($action === 'CREATE') {
                    $newProduct = Product::create([
                        'name' => $payload['name'] ?? 'Untitled Product',
                        'price' => floatval($payload['price'] ?? 0),
                        'description' => $payload['description'] ?? null,
                    ]);
                    $mappings[$tempId] = $newProduct->id;
                    $processedCount++;
                } elseif ($action === 'UPDATE') {
                    $productId = $payload['id'] ?? null;
                    if ($productId) {
                        $prod = Product::find($productId);
                        if ($prod) {
                            $prod->update([
                                'name' => $payload['name'] ?? $prod->name,
                                'price' => isset($payload['price']) ? floatval($payload['price']) : $prod->price,
                                'description' => $payload['description'] ?? $prod->description,
                            ]);
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'DELETE') {
                    $productId = $payload['id'] ?? null;
                    if ($productId) {
                        $prod = Product::find($productId);
                        if ($prod) {
                            $this->deleteProductImage($prod);
                            $prod->delete();
                            $processedCount++;
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Successfully synchronized {$processedCount} offline action(s).",
                'synced_count' => $processedCount,
                'mappings' => $mappings,
                'total_products' => Product::count(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BULK DELETE
    |--------------------------------------------------------------------------
    */

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        $products = Product::whereIn('id', $request->product_ids)->get();

        foreach ($products as $product) {
            $this->deleteProductImage($product);
            $product->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => count($request->product_ids) . ' product(s) deleted successfully.',
            ]);
        }

        return redirect()
            ->route('product.index')
            ->with('success', count($request->product_ids) . ' product(s) deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DUPLICATE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function duplicate(Product $product)
    {
        $duplicate = $product->replicate();
        $duplicate->name = $product->name . ' - Copy';
        $duplicate->save();

        return redirect()
            ->route('product.index')
            ->with('success', 'Product duplicated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT CSV
    |--------------------------------------------------------------------------
    */

    public function export(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->latest()->get();

        $fileName = 'products_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Name',
                'Description',
                'Price',
                'Created At',
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->description,
                    $product->price,
                    $product->created_at,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE PRODUCT IMAGE
    |--------------------------------------------------------------------------
    */

    private function deleteProductImage(Product $product)
    {
        if ($product->image && file_exists(public_path('products/' . $product->image))) {
            unlink(public_path('products/' . $product->image));
        }
    }
}
