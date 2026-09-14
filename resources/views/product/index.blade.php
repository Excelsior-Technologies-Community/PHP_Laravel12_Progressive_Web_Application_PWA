@extends('layouts.app')

@section('content')

<h2>All Products</h2>


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

    <span
        style="
            font-size:13px;
            color:#aaa;
        "
    >
        📦 Product list is PWA cache enabled
    </span>

</div>


@if($products->count() === 0)

    <div class="card">

        <p>
            No products available.
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
            min-width:750px;
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

                <td
                    style="
                        padding:12px;
                    "
                >
                    {{ $index + 1 }}
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

                        <a
                            class="btn btn-secondary"
                            href="{{ route('product.edit', $product) }}"
                        >
                            Edit
                        </a>


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

@endif


<script>

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