@extends('layouts.app')

@section('content')

<h2>
    All Products
</h2>

{{-- ========================================================= --}}
{{-- SUCCESS MESSAGE --}}
{{-- ========================================================= --}}

@if(session('success'))


<div
    style="
        background:rgba(25,135,84,0.15);
        border:1px solid rgba(25,135,84,0.35);
        color:#63df9b;
        padding:13px 16px;
        border-radius:12px;
        margin-bottom:20px;
    "
>
    ✅ {{ session('success') }}
</div>


@endif

{{-- ========================================================= --}}
{{-- VALIDATION ERRORS --}}
{{-- ========================================================= --}}

@if($errors->any())


<div
    style="
        background:rgba(220,53,69,0.15);
        border:1px solid rgba(220,53,69,0.35);
        color:#ff9da7;
        padding:13px 16px;
        border-radius:12px;
        margin-bottom:20px;
    "
>

    @foreach($errors->all() as $error)

        <div>
            {{ $error }}
        </div>

    @endforeach

</div>


@endif

{{-- ========================================================= --}}
{{-- STATISTICS --}}
{{-- ========================================================= --}}

<div
    style="
        display:grid;
        grid-template-columns:
            repeat(auto-fit,minmax(170px,1fr));
        gap:15px;
        margin-bottom:20px;
    "
>


<div class="card">

    <div style="font-size:13px;color:#aaa;">
        📦 Total Products
    </div>

    <h3>
        {{ $totalProducts }}
    </h3>

</div>


<div class="card">

    <div style="font-size:13px;color:#aaa;">
        📅 Added Today
    </div>

    <h3>
        {{ $todayProducts }}
    </h3>

</div>


<div class="card">

    <div style="font-size:13px;color:#aaa;">
        📊 Average Price
    </div>

    <h3 class="price">
        ₹{{ number_format($averagePrice, 2) }}
    </h3>

</div>


<div class="card">

    <div style="font-size:13px;color:#aaa;">
        ⬆️ Highest Price
    </div>

    <h3 class="price">
        ₹{{ number_format($highestPrice, 2) }}
    </h3>

</div>


<div class="card">

    <div style="font-size:13px;color:#aaa;">
        ⬇️ Lowest Price
    </div>

    <h3 class="price">
        ₹{{ number_format($lowestPrice, 2) }}
    </h3>

</div>


</div>

{{-- ========================================================= --}}
{{-- HEADER ACTIONS --}}
{{-- ========================================================= --}}

<div
    style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
        margin-bottom:20px;
    "
>


<a
    class="btn btn-primary"
    href="{{ route('product.create') }}"
>
    ➕ Add Product
</a>


<a
    class="btn btn-success"
    href="{{ route('product.export', request()->query()) }}"
>
    📥 Export CSV
</a>


<span
    style="
        font-size:13px;
        color:#aaa;
    "
>
    📦 Product list is PWA cache enabled
</span>


</div>

{{-- ========================================================= --}}
{{-- SEARCH / FILTER --}}
{{-- ========================================================= --}}

<div
    class="card"
    style="margin-bottom:20px;"
>


<form
    method="GET"
    action="{{ route('product.index') }}"
    style="
        background:none;
        padding:0;
        box-shadow:none;
    "
>

    <div
        style="
            display:grid;
            grid-template-columns:
                repeat(auto-fit,minmax(170px,1fr));
            gap:12px;
            align-items:end;
        "
    >

        {{-- SEARCH --}}

        <div>

            <label>
                🔎 Search
            </label>

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search product..."
                style="margin-bottom:0;"
            >

        </div>


        {{-- MIN PRICE --}}

        <div>

            <label>
                💰 Min Price
            </label>

            <input
                type="number"
                name="min_price"
                value="{{ $minPrice }}"
                min="0"
                step="0.01"
                placeholder="Minimum"
                style="margin-bottom:0;"
            >

        </div>


        {{-- MAX PRICE --}}

        <div>

            <label>
                💰 Max Price
            </label>

            <input
                type="number"
                name="max_price"
                value="{{ $maxPrice }}"
                min="0"
                step="0.01"
                placeholder="Maximum"
                style="margin-bottom:0;"
            >

        </div>


        {{-- SORT --}}

        <div>

            <label>
                ↕️ Sort
            </label>

            <select
                name="sort"
                style="
                    width:100%;
                    padding:12px;
                    margin-top:6px;
                    border-radius:12px;
                    border:none;
                    background:#1f1f1f;
                    color:#fff;
                "
            >

                <option
                    value="latest"
                    @selected($sort == 'latest')
                >
                    Latest
                </option>


                <option
                    value="oldest"
                    @selected($sort == 'oldest')
                >
                    Oldest
                </option>


                <option
                    value="name_asc"
                    @selected($sort == 'name_asc')
                >
                    Name A-Z
                </option>


                <option
                    value="name_desc"
                    @selected($sort == 'name_desc')
                >
                    Name Z-A
                </option>


                <option
                    value="price_low"
                    @selected($sort == 'price_low')
                >
                    Price Low-High
                </option>


                <option
                    value="price_high"
                    @selected($sort == 'price_high')
                >
                    Price High-Low
                </option>

            </select>

        </div>

    </div>


    <div
        class="actions"
        style="margin-top:18px;"
    >

        <button
            type="submit"
            class="btn btn-primary"
        >
            🔎 Apply Filters
        </button>


        <a
            href="{{ route('product.index') }}"
            class="btn btn-secondary"
        >
            ✖ Clear Filters
        </a>

    </div>

</form>


</div>

{{-- ========================================================= --}}
{{-- BULK ACTION BAR --}}
{{-- ========================================================= --}}

<div
    class="card"
    style="
        padding:14px 18px;
        margin-bottom:20px;
    "
>

<form
    id="bulkDeleteForm"
    method="POST"
    action="{{ route('product.bulkDelete') }}"
    style="
        background:none;
        padding:0;
        box-shadow:none;
    "
>

    @csrf

    <div
        style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
        "
    >

        <label
            style="
                display:flex;
                align-items:center;
                gap:8px;
                margin:0;
                cursor:pointer;
            "
        >

            <input
                type="checkbox"
                id="selectAll"
                style="
                    width:18px;
                    height:18px;
                    margin:0;
                "
            >

            <span>
                Select All
            </span>

        </label>


        <button
            type="submit"
            class="btn btn-danger"
            id="bulkDeleteButton"
            disabled
        >
            🗑️ Delete Selected
        </button>

    </div>


    <div
        id="selectedCount"
        style="
            margin-top:8px;
            color:#aaa;
            font-size:13px;
        "
    >
        0 products selected
    </div>

</form>


</div>

{{-- ========================================================= --}}
{{-- PRODUCT TABLE --}}
{{-- ========================================================= --}}

@if($products->count() === 0)


<div class="card">

    <p>
        No products found.
    </p>

</div>


@endif

@if($products->count() > 0)


<div
    class="card"
    style="
        padding:0;
        overflow-x:auto;
    "
>

    <table
        style="
            width:100%;
            border-collapse:collapse;
            min-width:1000px;
        "
    >

        <thead
            style="
                background:#f3f4f6;
            "
        >

            <tr>

                <th
                    style="
                        padding:12px;
                        color:#111;
                    "
                >
                    Select
                </th>


                <th
                    style="
                        padding:12px;
                        color:#111;
                    "
                >
                    #
                </th>


                <th
                    style="
                        padding:12px;
                        color:#111;
                    "
                >
                    Image
                </th>


                <th
                    style="
                        padding:12px;
                        color:#111;
                    "
                >
                    Name
                </th>


                <th
                    style="
                        padding:12px;
                        color:#111;
                    "
                >
                    Details
                </th>


                <th
                    style="
                        padding:12px;
                        color:#111;
                    "
                >
                    Price
                </th>


                <th
                    style="
                        padding:12px;
                        color:#111;
                    "
                >
                    Action
                </th>

            </tr>

        </thead>


        <tbody>

        @foreach($products as $index => $product)

            <tr
                style="
                    border-bottom:
                    1px solid #e5e7eb;
                "
            >

                {{-- CHECKBOX --}}

                <td
                    style="
                        padding:12px;
                        text-align:center;
                    "
                >

                    <input
                        type="checkbox"
                        name="product_ids[]"
                        value="{{ $product->id }}"
                        form="bulkDeleteForm"
                        class="product-checkbox"
                        style="
                            width:18px;
                            height:18px;
                            margin:0;
                        "
                    >

                </td>


                {{-- NUMBER --}}

                <td
                    style="
                        padding:12px;
                    "
                >

                    {{ $products->firstItem() + $index }}

                </td>


                {{-- IMAGE --}}

                <td
                    style="
                        padding:12px;
                    "
                >

                    @if($product->image)

                        <img
                            src="{{ asset('products/'.$product->image) }}"
                            alt="{{ $product->name }}"
                            style="
                                width:50px;
                                height:50px;
                                object-fit:cover;
                                border-radius:6px;
                            "
                        >

                    @else

                        -

                    @endif

                </td>


                {{-- NAME --}}

                <td
                    style="
                        padding:12px;
                        font-weight:600;
                    "
                >

                    {{ $product->name }}

                </td>


                {{-- DETAILS --}}

                <td
                    style="
                        padding:12px;
                        color:#555;
                        max-width:300px;
                    "
                >

                    {{ $product->description ?? '-' }}

                </td>


                {{-- PRICE --}}

                <td
                    style="
                        padding:12px;
                    "
                >

                    <span class="price">

                        ₹{{ number_format($product->price, 2) }}

                    </span>

                </td>


                {{-- ACTION --}}

                <td
                    style="
                        padding:12px;
                    "
                >

                    <div
                        class="actions"
                        style="
                            margin-top:0;
                        "
                    >

                        {{-- EDIT --}}

                        <a
                            class="btn btn-secondary"
                            href="{{ route('product.edit', $product) }}"
                        >
                            Edit
                        </a>


                        {{-- DUPLICATE --}}

                        <form
                            method="POST"
                            action="{{ route('product.duplicate', $product) }}"
                            style="
                                display:inline;
                                background:none;
                                padding:0;
                                box-shadow:none;
                            "
                        >

                            @csrf

                            <button
                                class="btn btn-success"
                                type="submit"
                                onclick="
                                    return confirm(
                                        'Duplicate this product?'
                                    )
                                "
                            >
                                Copy
                            </button>

                        </form>


                        {{-- DELETE --}}

                        <form
                            method="POST"
                            action="{{ route('product.destroy', $product) }}"
                            style="
                                display:inline;
                                background:none;
                                padding:0;
                                box-shadow:none;
                            "
                            onsubmit="
                                return confirm(
                                    'Delete this product?'
                                )
                            "
                        >

                            @csrf

                            @method('DELETE')

                            <button
                                class="btn btn-danger"
                                type="submit"
                            >
                                Delete
                            </button>

                        </form>

                    </div>

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>


{{-- ========================================================= --}}
{{-- NUMERIC PAGINATION ONLY --}}
{{-- ========================================================= --}}

@if($products->lastPage() > 1)

    <div
        style="
            display:flex;
            justify-content:center;
            align-items:center;
            gap:7px;
            flex-wrap:wrap;
            margin-top:20px;
            margin-bottom:10px;
        "
    >

        @for(
            $page = 1;
            $page <= $products->lastPage();
            $page++
        )

            @if($page == $products->currentPage())

                <span
                    style="
                        display:inline-flex;
                        align-items:center;
                        justify-content:center;
                        width:40px;
                        height:40px;
                        border-radius:10px;
                        text-decoration:none;
                        font-weight:600;
                        background:#0d6efd;
                        color:#fff;
                    "
                >
                    {{ $page }}
                </span>

            @else

                <a
                    href="{{ $products->url($page) }}"
                    style="
                        display:inline-flex;
                        align-items:center;
                        justify-content:center;
                        width:40px;
                        height:40px;
                        border-radius:10px;
                        text-decoration:none;
                        font-weight:600;
                        background:#111;
                        color:#aaa;
                        border:1px solid #333;
                    "
                >
                    {{ $page }}
                </a>

            @endif

        @endfor

    </div>

@endif


{{-- ========================================================= --}}
{{-- PAGINATION INFORMATION --}}
{{-- ========================================================= --}}

<div
    style="
        text-align:center;
        color:#888;
        font-size:13px;
        margin-bottom:20px;
    "
>

    Showing

    {{ $products->firstItem() }}

    -

    {{ $products->lastItem() }}

    of

    {{ $products->total() }}

    products

</div>

@endif

<script>

    /*
    |--------------------------------------------------------------------------
    | SELECT ALL / BULK DELETE
    |--------------------------------------------------------------------------
    */

    const selectAll =
        document.getElementById('selectAll');

    const checkboxes =
        document.querySelectorAll(
            '.product-checkbox'
        );

    const bulkButton =
        document.getElementById(
            'bulkDeleteButton'
        );

    const selectedCount =
        document.getElementById(
            'selectedCount'
        );


    function updateBulkActions() {

        const checked =
            document.querySelectorAll(
                '.product-checkbox:checked'
            );


        const count =
            checked.length;


        if (bulkButton) {

            bulkButton.disabled =
                count === 0;

        }


        if (selectedCount) {

            selectedCount.textContent =
                count +
                (
                    count === 1
                        ? ' product selected'
                        : ' products selected'
                );

        }


        if (
            selectAll &&
            checkboxes.length > 0 &&
            count === checkboxes.length
        ) {

            selectAll.checked = true;

        } else if (selectAll) {

            selectAll.checked = false;

        }

    }


    if (selectAll) {

        selectAll.addEventListener(
            'change',
            function () {

                checkboxes.forEach(
                    function (checkbox) {

                        checkbox.checked =
                            selectAll.checked;

                    }
                );


                updateBulkActions();

            }
        );

    }


    checkboxes.forEach(
        function (checkbox) {

            checkbox.addEventListener(
                'change',
                updateBulkActions
            );

        }
    );


    const bulkForm =
        document.getElementById(
            'bulkDeleteForm'
        );


    if (bulkForm) {

        bulkForm.addEventListener(
            'submit',
            function (event) {

                const count =
                    document.querySelectorAll(
                        '.product-checkbox:checked'
                    ).length;


                if (count === 0) {

                    event.preventDefault();

                    alert(
                        'Please select at least one product.'
                    );

                    return;

                }


                if (
                    !confirm(
                        'Delete ' +
                        count +
                        ' selected product(s)?'
                    )
                ) {

                    event.preventDefault();

                }

            }
        );

    }


    updateBulkActions();


    /*
    |--------------------------------------------------------------------------
    | OFFLINE ACTION WARNING
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function (event) {

            if (navigator.onLine) {
                return;
            }


            const target =
                event.target.closest(
                    'a[href*="/edit"]'
                );


            if (target) {

                event.preventDefault();

                alert(
                    'You are offline. Product editing requires an internet connection.'
                );

            }

        }
    );

</script>

@endsection
