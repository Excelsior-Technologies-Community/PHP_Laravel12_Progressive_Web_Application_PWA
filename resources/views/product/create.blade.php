@extends('layouts.app')

@section('content')

<h2>Add Product</h2>

<div id="createOfflineStatus" style="display:none; background:rgba(255,193,7,0.15); border:1px solid rgba(255,193,7,0.35); color:#ffd24d; padding:12px 16px; border-radius:12px; margin-bottom:20px; font-size:14px;">
    ⚡ <strong>Offline Mode Active:</strong> You can add products offline. It will be stored in your browser's IndexedDB and synced to the database automatically when online.
</div>

<form id="createProductForm" method="POST" enctype="multipart/form-data" action="{{ route('product.store') }}">
    @csrf

    {{-- PRODUCT NAME WITH BARCODE SCANNER --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
        <label style="margin:0;">
            Product Name / SKU <span style="color:#ff3b30;">*</span>
        </label>
        <button type="button" class="btn btn-warning" style="padding:4px 12px; font-size:12px; border-radius:16px;" onclick="window.openBarcodeScanner(function(code){ document.getElementById('productNameInput').value = code; })">
            📷 Scan Code to Fill
        </button>
    </div>

    <input
        type="text"
        id="productNameInput"
        name="name"
        value="{{ old('name') }}"
        placeholder="Product Name or scanned SKU"
        required
    >

    {{-- PRICE --}}
    <label>
        Price (₹) <span style="color:#ff3b30;">*</span>
    </label>

    <input
        type="number"
        name="price"
        id="productPriceInput"
        value="{{ old('price') }}"
        placeholder="0.00"
        step="0.01"
        min="0"
        required
        oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
    >

    {{-- DESCRIPTION --}}
    <label>
        Description
    </label>

    <textarea
        name="description"
        id="productDescriptionInput"
        placeholder="Enter product description, specifications, or notes..."
        rows="4"
    >{{ old('description') }}</textarea>

    {{-- IMAGE --}}
    <label id="imageLabel">
        Product Image <span style="font-size:12px; color:#888;">(Online only)</span>
    </label>

    <input
        type="file"
        name="image"
        accept=".jpg,.jpeg,.png,.webp"
    >

    {{-- ACTIONS --}}
    <div class="actions" style="margin-top:20px;">
        <button id="saveProductBtn" class="btn btn-primary" type="submit">
            💾 Save Product
        </button>

        <a href="{{ route('product.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </div>

</form>

<script>
    const offlineStatus = document.getElementById('createOfflineStatus');

    function checkCreateConnection() {
        if (!navigator.onLine) {
            offlineStatus.style.display = 'block';
        } else {
            offlineStatus.style.display = 'none';
        }
    }

    window.addEventListener('online', checkCreateConnection);
    window.addEventListener('offline', checkCreateConnection);
    checkCreateConnection();

    // Offline Interception for Product Creation
    document.getElementById('createProductForm').addEventListener('submit', async function (e) {
        if (!navigator.onLine) {
            e.preventDefault();

            const name = document.getElementById('productNameInput').value.trim();
            const price = document.getElementById('productPriceInput').value.trim();
            const description = document.getElementById('productDescriptionInput').value.trim();

            if (!name || !price) {
                alert('Please provide product name and price.');
                return;
            }

            await window.PwaEngine.queueOfflineAction('CREATE', {
                name: name,
                price: parseFloat(price),
                description: description
            });

            window.PwaEngine.showToast(`💾 "${name}" saved in IndexedDB! Will sync when online.`, 'success');

            setTimeout(() => {
                window.location.href = "{{ route('product.index') }}";
            }, 1000);
        }
    });
</script>

@endsection
