@extends('layouts.app')

@section('content')

<h2>Add Product</h2>


<div
    id="createOfflineWarning"
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
    Adding a product requires an internet connection.
</div>


<form
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('product.store') }}"
>

    @csrf


    {{-- PRODUCT NAME --}}

    <label>
        Product Name
    </label>

    <input
        type="text"
        name="name"
        value="{{ old('name') }}"
        placeholder="Product Name"
        required
    >


    {{-- PRICE --}}

    <label>
        Price
    </label>

    <input
        type="number"
        name="price"
        value="{{ old('price') }}"
        placeholder="Price"
        step="0.01"
        min="0"
        required
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
        placeholder="Description"
        rows="4"
    >{{ old('description') }}</textarea>


    {{-- IMAGE --}}

    <label>
        Product Image
    </label>

    <input
        type="file"
        name="image"
        accept=".jpg,.jpeg,.png,.webp"
    >


    {{-- ACTIONS --}}

    <div class="actions">

        <button
            id="saveProductBtn"
            class="btn btn-primary"
            type="submit"
        >
            Save Product
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
            'createOfflineWarning'
        );

    const saveButton =
        document.getElementById(
            'saveProductBtn'
        );


    function checkCreateConnection() {

        if (navigator.onLine) {

            warning.style.display = 'none';

            saveButton.disabled = false;

            saveButton.style.opacity = '1';

        } else {

            warning.style.display = 'block';

            saveButton.disabled = true;

            saveButton.style.opacity = '0.5';

        }

    }


    window.addEventListener(
        'online',
        checkCreateConnection
    );

    window.addEventListener(
        'offline',
        checkCreateConnection
    );


    checkCreateConnection();

</script>

@endsection