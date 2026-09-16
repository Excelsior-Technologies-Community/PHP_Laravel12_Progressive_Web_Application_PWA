@extends('layouts.app')

@section('content')

<h2>Edit Product</h2>

<div id="editOfflineStatus" style="display:none; background:rgba(255,193,7,0.15); border:1px solid rgba(255,193,7,0.35); color:#ffd24d; padding:12px 16px; border-radius:12px; margin-bottom:20px; font-size:14px;">
    ⚡ <strong>Offline Mode Active:</strong> You can edit products offline. Changes will be saved in IndexedDB and synchronized to the database automatically when online.
</div>

<form id="editProductForm" method="POST" action="{{ route('product.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- PRODUCT NAME WITH BARCODE SCANNER --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
        <label style="margin:0;">
            Product Name / SKU <span style="color:#ff3b30;">*</span>
        </label>
        <button type="button" class="btn btn-warning" style="padding:4px 12px; font-size:12px; border-radius:16px;" onclick="window.openBarcodeScanner(function(code){ document.getElementById('editProductNameInput').value = code; })">
            📷 Scan Code to Fill
        </button>
    </div>

    <input
        type="text"
        id="editProductNameInput"
        name="name"
        value="{{ old('name', $product->name) }}"
        required
        placeholder="Product Name"
    >

    {{-- PRICE --}}
    <label>
        Price (₹) <span style="color:#ff3b30;">*</span>
    </label>

    <input
        type="number"
        name="price"
        id="editProductPriceInput"
        step="0.01"
        min="0"
        value="{{ old('price', $product->price) }}"
        required
        placeholder="Price"
        oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
    >

    {{-- DESCRIPTION --}}
    <label>
        Description
    </label>

    <textarea
        name="description"
        id="editProductDescriptionInput"
        rows="4"
        placeholder="Product Description"
    >{{ old('description', $product->description) }}</textarea>

    {{-- IMAGE --}}
    <label>
        Change Image <span style="font-size:12px; color:#888;">(Online only)</span>
    </label>

    <input
        type="file"
        name="image"
        accept=".jpg,.jpeg,.png,.webp"
    >

    {{-- CURRENT IMAGE --}}
    @if($product->image)
        <p style="margin:10px 0 5px; font-size:13px; color:#aaa;">
            Current Image
        </p>

        <img
            src="{{ asset('products/'.$product->image) }}"
            alt="{{ $product->name }}"
            style="width:140px; height:140px; object-fit:cover; margin-top:5px; border-radius:12px;"
        >
    @endif

    {{-- ACTION BUTTONS --}}
    <div class="actions" style="margin-top:20px;">
        <button id="updateProductBtn" type="submit" class="btn btn-primary">
            💾 Update Product
        </button>

        <a href="{{ route('product.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </div>

</form>

<script>
    const offlineStatus = document.getElementById('editOfflineStatus');

    function checkEditConnection() {
        if (!navigator.onLine) {
            offlineStatus.style.display = 'block';
        } else {
            offlineStatus.style.display = 'none';
        }
    }

    window.addEventListener('online', checkEditConnection);
    window.addEventListener('offline', checkEditConnection);
    checkEditConnection();

    // Offline Interception for Product Update
    document.getElementById('editProductForm').addEventListener('submit', async function (e) {
        if (!navigator.onLine) {
            e.preventDefault();

            const name = document.getElementById('editProductNameInput').value.trim();
            const price = document.getElementById('editProductPriceInput').value.trim();
            const description = document.getElementById('editProductDescriptionInput').value.trim();

            if (!name || !price) {
                alert('Please provide product name and price.');
                return;
            }

            await window.PwaEngine.queueOfflineAction('UPDATE', {
                id: {{ $product->id }},
                name: name,
                price: parseFloat(price),
                description: description
            });

            window.PwaEngine.showToast(`💾 Changes for "${name}" saved in IndexedDB! Will sync when online.`, 'success');

            setTimeout(() => {
                window.location.href = "{{ route('product.index') }}";
            }, 1000);
        }
    });
</script>

@endsection
