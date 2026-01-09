<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get(); // Get all products
        return view('product.index', compact('products')); // Show list
    }

    public function create()
    {
        return view('product.create'); // Show create form
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',                       // Validate name
            'price' => 'required|numeric',              // Validate price
            'description' => 'nullable',                // Optional details
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension(); // Image name
            $request->image->move(public_path('products'), $imageName); // Save image
            $data['image'] = $imageName; // Store filename
        }

        Product::create($data); // Save product

        return redirect()->route('product.index'); // Redirect to list
    }

    public function edit(Product $product)
    {
        return view('product.edit', compact('product')); // Show edit form
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required',        // Validate name
            'price' => 'required|numeric',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        if ($request->hasFile('image')) {

            if ($product->image && file_exists(public_path('products/'.$product->image))) {
                unlink(public_path('products/'.$product->image)); // Delete old image
            }

            $imageName = time().'.'.$request->image->extension(); // New image name
            $request->image->move(public_path('products'), $imageName); // Save image
            $data['image'] = $imageName;
        }

        $product->update($data); // Update product

        return redirect()->route('product.index'); // Redirect back
    }

    public function destroy(Product $product)
    {
        if ($product->image && file_exists(public_path('products/'.$product->image))) {
            unlink(public_path('products/'.$product->image)); // Delete image
        }

        $product->delete(); // Delete product

        return redirect()->route('product.index'); // Redirect to list
    }
}
