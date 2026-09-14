@extends('layouts.app')

@section('content')

<h2>Edit Product</h2>


<div
    id="editOfflineWarning"
    style="
        display:none;
        background:rgba(220,53,69,0.15);
        border:1px solid rgba(220,53,69,0.35);
        color:#ff9da7;
        padding:12px 16px;
        border-radius:12px;
        margin-bottom:20px;
    "
>
    🔴 You are offline.
    Updating a product requires an internet connection.
</div>


<form
    method="POST"
    action="{{ route('product.update', $product) }}"
    enctype="multipart/form-data"
>

    @csrf

    @method('PUT')


    {{-- PRODUCT NAME --}}

    <label>
        Product Name
    </label>

    <input
        type="text"
        name="name"
        value="{{ old('name', $product->name) }}"
        required
        placeholder="Product Name"
    >


    {{-- PRICE --}}

    <label>
        Price
    </label>

    <input
        type="number"
        name="price"
        step="0.01"
        min="0"
        value="{{ old('price', $product->price) }}"
        required
        placeholder="Price"
        oninput="
            this.value =
            this.value.replace(
                /[^0-9.]/g,
                ''
            )
        "
    >


    {{-- DESCRIPTION --}}

    <label>
        Description
    </label>

    <textarea
        name="description"
        rows="4"
        placeholder="Product Description"
    >{{ old('description', $product->description) }}</textarea>


    {{-- IMAGE --}}

    <label>
        Change Image
    </label>

    <input
        type="file"
        name="image"
        accept=".jpg,.jpeg,.png,.webp"
    >


    {{-- CURRENT IMAGE --}}

    @if($product->image)

        <p>
            Current Image
        </p>

        <img
            src="{{ asset('products/'.$product->image) }}"
            alt="{{ $product->name }}"
            style="
                width:160px;
                height:160px;
                object-fit:cover;
                margin-top:10px;
                border-radius:12px;
            "
        >

    @endif


    {{-- ACTION BUTTONS --}}

    <div
        class="actions"
        style="margin-top:20px;"
    >

        <button
            id="updateProductBtn"
            type="submit"
            class="btn btn-primary"
        >
            Update Product
        </button>


        <a
            href="{{ route('product.index') }}"
            class="btn btn-secondary"
        >
            Cancel
        </a>

    </div>

</form>


<script>

    const warning =
        document.getElementById(
            'editOfflineWarning'
        );

    const updateButton =
        document.getElementById(
            'updateProductBtn'
        );


    function checkEditConnection() {

        if (navigator.onLine) {

            warning.style.display = 'none';

            updateButton.disabled = false;

            updateButton.style.opacity = '1';

        } else {

            warning.style.display = 'block';

            updateButton.disabled = true;

            updateButton.style.opacity = '0.5';

        }

    }


    window.addEventListener(
        'online',
        checkEditConnection
    );

    window.addEventListener(
        'offline',
        checkEditConnection
    );


    checkEditConnection();

</script>

@endsection