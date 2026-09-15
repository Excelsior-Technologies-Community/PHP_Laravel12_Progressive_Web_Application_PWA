<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PRODUCT INDEX
    |--------------------------------------------------------------------------
    | Features:
    | - Search
    | - Price filter
    | - Sorting
    | - Pagination
    | - Statistics
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        $search = $request->input('search');

        /*
        |--------------------------------------------------------------------------
        | PRICE FILTER
        |--------------------------------------------------------------------------
        */

        $minPrice = $request->input('min_price');

        $maxPrice = $request->input('max_price');

        /*
        |--------------------------------------------------------------------------
        | SORTING
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | PRODUCT QUERY
        |--------------------------------------------------------------------------
        */

        $query = Product::query();

        /*
        |--------------------------------------------------------------------------
        | SEARCH BY NAME / DESCRIPTION
        |--------------------------------------------------------------------------
        */

        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere(
                        'description',
                        'like',
                        '%' . $search . '%'
                    );

            });
        }

        /*
        |--------------------------------------------------------------------------
        | MINIMUM PRICE
        |--------------------------------------------------------------------------
        */

        if ($minPrice !== null && $minPrice !== '') {

            $query->where(
                'price',
                '>=',
                $minPrice
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MAXIMUM PRICE
        |--------------------------------------------------------------------------
        */

        if ($maxPrice !== null && $maxPrice !== '') {

            $query->where(
                'price',
                '<=',
                $maxPrice
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        */

        switch ($sort) {

            case 'oldest':

                $query->oldest();

                break;

            case 'name_asc':

                $query->orderBy(
                    'name',
                    'asc'
                );

                break;

            case 'name_desc':

                $query->orderBy(
                    'name',
                    'desc'
                );

                break;

            case 'price_low':

                $query->orderBy(
                    'price',
                    'asc'
                );

                break;

            case 'price_high':

                $query->orderBy(
                    'price',
                    'desc'
                );

                break;

            default:

                $query->latest();

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $products = $query
            ->paginate(5)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | STATISTICS
        |--------------------------------------------------------------------------
        */

        $totalProducts = Product::count();

        $todayProducts = Product::whereDate(
            'created_at',
            today()
        )->count();

        $averagePrice = Product::avg('price') ?? 0;

        $highestPrice = Product::max('price') ?? 0;

        $lowestPrice = Product::min('price') ?? 0;

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

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

            'name' =>
                'required|string|max:255',

            'price' =>
                'required|numeric|min:0',

            'description' =>
                'nullable|string',

            'image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);


        /*
        |--------------------------------------------------------------------------
        | IMAGE UPLOAD
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            $imageName =
                time() .
                '_' .
                uniqid() .
                '.' .
                $request->image->extension();

            $request->image->move(
                public_path('products'),
                $imageName
            );

            $data['image'] = $imageName;
        }


        Product::create($data);


        return redirect()
            ->route('product.index')
            ->with(
                'success',
                'Product created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Product $product)
    {
        return view(
            'product.edit',
            compact('product')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Product $product
    ) {

        $data = $request->validate([

            'name' =>
                'required|string|max:255',

            'price' =>
                'required|numeric|min:0',

            'description' =>
                'nullable|string',

            'image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);


        /*
        |--------------------------------------------------------------------------
        | NEW IMAGE
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            /*
            | Delete old image
            */

            if (
                $product->image &&
                file_exists(
                    public_path(
                        'products/' .
                        $product->image
                    )
                )
            ) {

                unlink(
                    public_path(
                        'products/' .
                        $product->image
                    )
                );
            }


            /*
            | Save new image
            */

            $imageName =
                time() .
                '_' .
                uniqid() .
                '.' .
                $request->image->extension();

            $request->image->move(
                public_path('products'),
                $imageName
            );

            $data['image'] = $imageName;
        }


        $product->update($data);


        return redirect()
            ->route('product.index')
            ->with(
                'success',
                'Product updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE SINGLE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function destroy(Product $product)
    {
        $this->deleteProductImage($product);

        $product->delete();


        return redirect()
            ->route('product.index')
            ->with(
                'success',
                'Product deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | BULK DELETE
    |--------------------------------------------------------------------------
    */

    public function bulkDelete(Request $request)
    {
        $request->validate([

            'product_ids' =>
                'required|array|min:1',

            'product_ids.*' =>
                'integer|exists:products,id',

        ]);


        $products = Product::whereIn(
            'id',
            $request->product_ids
        )->get();


        foreach ($products as $product) {

            $this->deleteProductImage($product);

            $product->delete();
        }


        return redirect()
            ->route('product.index')
            ->with(
                'success',
                count($request->product_ids) .
                ' product(s) deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DUPLICATE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function duplicate(Product $product)
    {
        $duplicate = $product->replicate();

        $duplicate->name =
            $product->name . ' - Copy';

        $duplicate->save();


        return redirect()
            ->route('product.index')
            ->with(
                'success',
                'Product duplicated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT CSV
    |--------------------------------------------------------------------------
    */

    public function export(Request $request)
    {
        $query = Product::query();


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'description',
                    'like',
                    '%' . $search . '%'
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | MIN PRICE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('min_price')) {

            $query->where(
                'price',
                '>=',
                $request->min_price
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MAX PRICE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('max_price')) {

            $query->where(
                'price',
                '<=',
                $request->max_price
            );
        }


        $products = $query
            ->latest()
            ->get();


        /*
        |--------------------------------------------------------------------------
        | CSV FILE
        |--------------------------------------------------------------------------
        */

        $fileName =
            'products_' .
            now()->format('Y_m_d_H_i_s') .
            '.csv';


        $headers = [

            'Content-Type' =>
                'text/csv',

            'Content-Disposition' =>
                'attachment; filename="' .
                $fileName .
                '"',

        ];


        $callback = function () use ($products) {

            $file = fopen(
                'php://output',
                'w'
            );


            /*
            | CSV HEADER
            */

            fputcsv(
                $file,
                [
                    'ID',
                    'Name',
                    'Description',
                    'Price',
                    'Created At',
                ]
            );


            /*
            | CSV DATA
            */

            foreach ($products as $product) {

                fputcsv(
                    $file,
                    [
                        $product->id,
                        $product->name,
                        $product->description,
                        $product->price,
                        $product->created_at,
                    ]
                );
            }


            fclose($file);
        };


        return Response::stream(
            $callback,
            200,
            $headers
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE PRODUCT IMAGE
    |--------------------------------------------------------------------------
    */

    private function deleteProductImage(Product $product)
    {
        if (
            $product->image &&
            file_exists(
                public_path(
                    'products/' .
                    $product->image
                )
            )
        ) {

            unlink(
                public_path(
                    'products/' .
                    $product->image
                )
            );
        }
    }
}