@extends('layouts.app')

@section('content')

<h2>Edit Product</h2>

<form method="POST"
      action="{{ route('product.update', $product) }}"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- PRODUCT NAME --}}
    <label>Product Name</label>
    <input
        type="text"
        name="name"
        value="{{ old('name', $product->name) }}"
        required
        placeholder="Product Name"
    >

    {{-- PRICE (NUMERIC ONLY) --}}
    <label>Price</label>
    <input
        type="number"
        name="price"
        step="0.01"
        min="0"
        value="{{ old('price', $product->price) }}"
        required
        placeholder="Price"
        oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
    >

    {{-- DESCRIPTION --}}
    <label>Description</label>
    <textarea
        name="description"
        rows="4"
        placeholder="Product Description"
    >{{ old('description', $product->description) }}</textarea>

    {{-- IMAGE --}}
    <label>Change Image</label>
    <input type="file" name="image">

    {{-- IMAGE PREVIEW --}}
    @if($product->image)
        <img
            src="{{ asset('products/'.$product->image) }}"
            style="width:160px;margin-top:10px;border-radius:12px;"
        >
    @endif

    {{-- ACTION BUTTONS --}}
    <div class="actions" style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">
            Update Product
        </button>

        <a href="{{ route('product.index') }}"
           class="btn btn-secondary">
            Cancel
        </a>
    </div>

</form>

@endsection
