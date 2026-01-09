@extends('layouts.app')

@section('content')

<h2>Edit Product</h2>

<form method="POST"
      action="{{ route('product.update', $product) }}"
      enctype="multipart/form-data">

    @csrf
    @method('PUT')

    <label>Product Name</label>
    <input type="text"
           name="name"
           value="{{ old('name', $product->name) }}"
           required>

    <label>Price</label>
    <input type="number"
           step="0.01"
           name="price"
           value="{{ old('price', $product->price) }}"
           required>

    <label>Description</label>
    <textarea name="description"
              rows="4">{{ old('description', $product->description) }}</textarea>

    <label>Change Image</label>
    <input type="file" name="image">

    {{-- IMAGE PREVIEW --}}
    @if($product->image)
        <img src="{{ asset('products/'.$product->image) }}"
             style="width:160px;margin-top:10px;border-radius:12px;">
    @endif

    <div class="actions" style="margin-top:20px;">
        <button class="btn btn-primary">Update Product</button>
        <a class="btn btn-secondary" href="{{ route('product.index') }}">Cancel</a>
    </div>

</form>

@endsection
