@extends('layouts.app')

@section('content')

<h2>
    All Products
</h2>

{{-- ========================================================= --}}
{{-- SUCCESS MESSAGE --}}
{{-- ========================================================= --}}

@if(session('success'))
<div style="background:rgba(25,135,84,0.15); border:1px solid rgba(25,135,84,0.35); color:#63df9b; padding:13px 16px; border-radius:12px; margin-bottom:20px;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- ========================================================= --}}
{{-- VALIDATION ERRORS --}}
{{-- ========================================================= --}}

@if($errors->any())
<div style="background:rgba(220,53,69,0.15); border:1px solid rgba(220,53,69,0.35); color:#ff9da7; padding:13px 16px; border-radius:12px; margin-bottom:20px;">
    @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
</div>
@endif

{{-- ========================================================= --}}
{{-- STATISTICS --}}
{{-- ========================================================= --}}

<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:15px; margin-bottom:20px;">

    <div class="card">
        <div style="font-size:13px;color:#aaa;">📦 Total Products</div>
        <h3 id="totalProductsCount">{{ $totalProducts }}</h3>
    </div>

    <div class="card">
        <div style="font-size:13px;color:#aaa;">📅 Added Today</div>
        <h3>{{ $todayProducts }}</h3>
    </div>

    <div class="card">
        <div style="font-size:13px;color:#aaa;">📊 Average Price</div>
        <h3 class="price">₹{{ number_format($averagePrice, 2) }}</h3>
    </div>

    <div class="card">
        <div style="font-size:13px;color:#aaa;">⬆️ Highest Price</div>
        <h3 class="price">₹{{ number_format($highestPrice, 2) }}</h3>
    </div>

    <div class="card">
        <div style="font-size:13px;color:#aaa;">⬇️ Lowest Price</div>
        <h3 class="price">₹{{ number_format($lowestPrice, 2) }}</h3>
    </div>

</div>

{{-- ========================================================= --}}
{{-- HEADER ACTIONS --}}
{{-- ========================================================= --}}

<div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:20px;">

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn btn-primary" href="{{ route('product.create') }}">
            ➕ Add Product
        </a>

        <button type="button" class="btn btn-warning" onclick="window.openBarcodeScanner(function(code){ document.querySelector('input[name=search]').value = code; document.getElementById('searchForm').submit(); })">
            📷 Scan Barcode / QR
        </button>

        <a class="btn btn-success" href="{{ route('product.export', request()->query()) }}">
            📥 Export CSV
        </a>
    </div>

    <span style="font-size:13px; color:#aaa;">
        📦 Offline-First & Background Sync Enabled
    </span>

</div>

{{-- ========================================================= --}}
{{-- SEARCH / FILTER --}}
{{-- ========================================================= --}}

<div class="card" style="margin-bottom:20px;">

<form id="searchForm" method="GET" action="{{ route('product.index') }}" style="background:none; padding:0; box-shadow:none;">

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:12px; align-items:end;">

        {{-- SEARCH --}}
        <div>
            <label style="display:flex; justify-content:space-between; align-items:center;">
                <span>🔎 Search</span>
                <a href="javascript:void(0)" onclick="window.openBarcodeScanner(function(code){ document.querySelector('input[name=search]').value = code; document.getElementById('searchForm').submit(); })" style="font-size:11px; color:#ffc107;">
                    📷 Scan
                </a>
            </label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search name or barcode..." style="margin-bottom:0;">
        </div>

        {{-- MIN PRICE --}}
        <div>
            <label>💰 Min Price</label>
            <input type="number" name="min_price" value="{{ $minPrice }}" min="0" step="0.01" placeholder="0" style="margin-bottom:0;">
        </div>

        {{-- MAX PRICE --}}
        <div>
            <label>💰 Max Price</label>
            <input type="number" name="max_price" value="{{ $maxPrice }}" min="0" step="0.01" placeholder="9999" style="margin-bottom:0;">
        </div>

        {{-- SORT --}}
        <div>
            <label>🔃 Sort By</label>
            <select name="sort" style="margin-bottom:0;">
                <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Latest</option>
                <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                <option value="price_low" {{ $sort === 'price_low' ? 'selected' : '' }}>Price Low to High</option>
                <option value="price_high" {{ $sort === 'price_high' ? 'selected' : '' }}>Price High to Low</option>
            </select>
        </div>

        {{-- BUTTONS --}}
        <div style="display:flex; gap:8px;">
            <button type="submit" class="btn btn-primary" style="flex:1;">
                Filter
            </button>
            <a href="{{ route('product.index') }}" class="btn btn-secondary">
                Reset
            </a>
        </div>

    </div>

</form>

</div>

{{-- ========================================================= --}}
{{-- BULK ACTIONS --}}
{{-- ========================================================= --}}

<div class="card" style="margin-bottom:20px;">

<form id="bulkDeleteForm" method="POST" action="{{ route('product.bulkDelete') }}" style="background:none; padding:0; box-shadow:none;">
    @csrf

    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">

        <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer;">
            <input type="checkbox" id="selectAll" style="width:18px; height:18px; margin:0;">
            <span>Select All</span>
        </label>

        <button type="submit" class="btn btn-danger" id="bulkDeleteButton" disabled>
            🗑️ Delete Selected
        </button>

    </div>

    <div id="selectedCount" style="margin-top:8px; color:#aaa; font-size:13px;">
        0 products selected
    </div>

</form>

</div>

{{-- ========================================================= --}}
{{-- PRODUCT TABLE --}}
{{-- ========================================================= --}}

@if($products->count() === 0)
<div class="card">
    <p>No products found.</p>
</div>
@endif

@if($products->count() > 0)

<div class="card" style="padding:0; overflow-x:auto;">

    <table style="width:100%; border-collapse:collapse; min-width:1000px;">
        <thead style="background:#1f1f1f; color:#ddd; border-bottom:1px solid #333;">
            <tr>
                <th style="padding:12px;">Select</th>
                <th style="padding:12px;">#</th>
                <th style="padding:12px;">Image</th>
                <th style="padding:12px;">Name</th>
                <th style="padding:12px;">Details</th>
                <th style="padding:12px;">Price</th>
                <th style="padding:12px; text-align:center;">Actions</th>
            </tr>
        </thead>

        <tbody>
        @foreach($products as $index => $product)
            <tr id="product-row-{{ $product->id }}" style="border-bottom:1px solid #282828;">

                {{-- CHECKBOX --}}
                <td style="padding:12px; text-align:center;">
                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" form="bulkDeleteForm" class="product-checkbox" style="width:16px; height:16px; margin:0;">
                </td>

                {{-- INDEX --}}
                <td style="padding:12px; color:#888;">
                    {{ $products->firstItem() + $index }}
                </td>

                {{-- IMAGE --}}
                <td style="padding:12px;">
                    @if($product->image)
                        <img src="{{ asset('products/' . $product->image) }}" alt="{{ $product->name }}" style="width:48px; height:48px; object-fit:cover; border-radius:8px; margin:0;">
                    @else
                        <div style="width:48px; height:48px; border-radius:8px; background:#222; display:flex; align-items:center; justify-content:center; color:#666; font-size:18px;">
                            📦
                        </div>
                    @endif
                </td>

                {{-- NAME --}}
                <td style="padding:12px;">
                    <div style="font-weight:600; color:#fff;">
                        {{ $product->name }}
                    </div>
                </td>

                {{-- DESCRIPTION --}}
                <td style="padding:12px; color:#aaa; font-size:13px; max-width:240px;">
                    {{ $product->description ? Str::limit($product->description, 45) : 'No description' }}
                </td>

                {{-- PRICE --}}
                <td style="padding:12px;">
                    <span class="price">₹{{ number_format($product->price, 2) }}</span>
                </td>

                {{-- ACTIONS --}}
                <td style="padding:12px; text-align:center;">
                    <div style="display:flex; justify-content:center; gap:6px; flex-wrap:wrap;">

                        {{-- WEB SHARE BUTTON --}}
                        <button type="button" class="btn btn-outline" style="padding:6px 12px; font-size:12px; border-radius:18px;" onclick="window.shareProduct('{{ addslashes($product->name) }}', '{{ $product->price }}', '{{ url('/product?search=' . urlencode($product->name)) }}')" title="Share via Web Share API">
                            📤 Share
                        </button>

                        {{-- EDIT --}}
                        <a class="btn btn-secondary" style="padding:6px 12px; font-size:12px; border-radius:18px;" href="{{ route('product.edit', $product) }}">
                            ✏️ Edit
                        </a>

                        {{-- DUPLICATE --}}
                        <form method="POST" action="{{ route('product.duplicate', $product) }}" style="display:inline; background:none; padding:0; box-shadow:none;">
                            @csrf
                            <button class="btn btn-success" style="padding:6px 12px; font-size:12px; border-radius:18px;" type="submit" onclick="return confirm('Duplicate this product?')">
                                📋 Copy
                            </button>
                        </form>

                        {{-- DELETE (OFFLINE AWARE) --}}
                        <form method="POST" action="{{ route('product.destroy', $product) }}" class="delete-product-form" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" style="display:inline; background:none; padding:0; box-shadow:none;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" style="padding:6px 12px; font-size:12px; border-radius:18px;" type="submit">
                                🗑️ Delete
                            </button>
                        </form>

                    </div>
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>

</div>

{{-- NUMERIC PAGINATION --}}
@if($products->lastPage() > 1)
    <div style="display:flex; justify-content:center; align-items:center; gap:7px; flex-wrap:wrap; margin-top:20px; margin-bottom:10px;">
        @for($page = 1; $page <= $products->lastPage(); $page++)
            @if($page == $products->currentPage())
                <span style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:10px; font-weight:600; background:#0d6efd; color:#fff;">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $products->url($page) }}" style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:10px; font-weight:600; background:#111; color:#aaa; border:1px solid #333;">
                    {{ $page }}
                </a>
            @endif
        @endfor
    </div>
@endif

<div style="text-align:center; color:#888; font-size:13px; margin-bottom:20px;">
    Showing {{ $products->firstItem() }} - {{ $products->lastItem() }} of {{ $products->total() }} products
</div>

@endif

<script>
    // App Badging API Initialization
    if (window.updateAppBadge) {
        window.updateAppBadge({{ $totalProducts }});
    }

    // Select All / Bulk Delete Logic
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const bulkButton = document.getElementById('bulkDeleteButton');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkActions() {
        const checked = document.querySelectorAll('.product-checkbox:checked');
        const count = checked.length;
        if (bulkButton) bulkButton.disabled = count === 0;
        if (selectedCount) selectedCount.textContent = count + (count === 1 ? ' product selected' : ' products selected');
        if (selectAll && checkboxes.length > 0) selectAll.checked = count === checkboxes.length;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkActions();
        });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateBulkActions));

    // Offline-Aware Delete Handling
    document.querySelectorAll('.delete-product-form').forEach(form => {
        form.addEventListener('submit', async function (e) {
            const prodId = this.getAttribute('data-product-id');
            const prodName = this.getAttribute('data-product-name');

            if (!confirm(`Delete product "${prodName}"?`)) {
                e.preventDefault();
                return;
            }

            if (!navigator.onLine) {
                e.preventDefault();
                await window.PwaEngine.queueOfflineAction('DELETE', { id: prodId, name: prodName });

                const row = document.getElementById(`product-row-${prodId}`);
                if (row) {
                    row.style.transition = 'all 0.4s ease';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 400);
                }

                window.PwaEngine.showToast(`🗑️ "${prodName}" deleted locally. Will sync when online!`, 'info');
            }
        });
    });
</script>

@endsection
