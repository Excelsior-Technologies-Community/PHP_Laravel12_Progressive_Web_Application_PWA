@extends('layouts.app')

@section('content')

<h2>Add Product</h2>

<form method="POST" enctype="multipart/form-data" action="{{ route('product.store') }}">
    @csrf

    {{-- PRODUCT NAME --}}
    <input
        type="text"
        name="name"
        placeholder="Product Name"
        required
    >

    {{-- PRICE (ONLY NUMERIC ALLOWED) --}}
    <input
        type="number"
        name="price"
        placeholder="Price"
        step="0.01"
        min="0"
        required
        oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
    >

    {{-- DESCRIPTION (NOW VISIBLE & WORKING) --}}
    <textarea
        name="description"
        placeholder="Description"
        rows="4"
    ></textarea>

    {{-- IMAGE --}}
    <input type="file" name="image">

    <button class="btn btn-primary">Save Product</button>
</form>

@endsection
