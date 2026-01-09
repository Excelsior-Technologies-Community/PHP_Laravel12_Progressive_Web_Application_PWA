@extends('layouts.app')

@section('content')

<h2>Add Product</h2>

<form method="POST" enctype="multipart/form-data" action="{{ route('product.store') }}">
    @csrf

    <input name="name" placeholder="Product Name" required>

    <input name="price" placeholder="Price" required>

    <textarea name="description" placeholder="Description"></textarea>

    <input type="file" name="image">

    <button class="btn btn-primary">Save Product</button>
</form>

@endsection
