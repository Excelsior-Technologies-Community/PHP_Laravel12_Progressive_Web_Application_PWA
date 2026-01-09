@extends('layouts.app')

@section('content')

<h2>All Products</h2>

<a class="btn btn-primary" href="{{ route('product.create') }}">➕ Add Product</a>

<br><br>

@if($products->count() === 0)
    <p>No products available.</p>
@endif

<div class="card" style="padding:0;">
    <table style="width:100%; border-collapse:collapse;">
        <thead style="background:#f3f4f6;">
            <tr>
                <th style="padding:12px; color:#111;">#</th>
                <th style="padding:12px; color:#111;">Image</th>
                <th style="padding:12px; color:#111;">Name</th>
                <th style="padding:12px; color:#111;">Details</th>
                <th style="padding:12px; color:#111;">Price</th>
                <th style="padding:12px; color:#111;">Action</th>
            </tr>
        </thead>


        <tbody>
        @foreach($products as $index => $product)
            <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:12px;">{{ $index + 1 }}</td>

                {{-- IMAGE --}}
                <td style="padding:12px;">
                    @if($product->image)
                        <img src="{{ asset('products/'.$product->image) }}"
                             style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                    @else
                        -
                    @endif
                </td>

                {{-- NAME --}}
                <td style="padding:12px; font-weight:600;">
                    {{ $product->name }}
                </td>

                {{-- DETAILS --}}
                <td style="padding:12px; color:#555;">
                    {{ $product->description ?? '-' }}
                </td>

                {{-- PRICE --}}
                <td style="padding:12px;">
                    ₹{{ number_format($product->price, 2) }}
                </td>

                {{-- ACTION --}}
                <td style="padding:12px;">
                    <a class="btn btn-secondary btn-sm"
                       href="{{ route('product.edit', $product) }}">
                        Edit
                    </a>

                    <form method="POST"
                          action="{{ route('product.destroy', $product) }}"
                          style="display:inline;"
                          onsubmit="return confirm('Delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection
