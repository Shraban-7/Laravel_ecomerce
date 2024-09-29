@extends('frontend.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                <div class="col-md-3">
                    @include('frontend.partials.account-pannel')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">My Orders</h2>
                        </div>
                        <div class="card-body p-4">

                            @forelse ($wishlistItems as $item)
                                @php
                                    $product_img = getProductImage($item->product->id);
                                @endphp
                                <div class="d-sm-flex justify-content-between mt-lg-4 mb-4 pb-3 pb-sm-2 border-bottom">
                                    <div class="d-block d-sm-flex align-items-start text-center text-sm-start">
                                        <a class="d-block flex-shrink-0 mx-auto me-sm-4"
                                            href="{{ route('frontend.product', $item->product->id) }}"
                                            style="width: 10rem;">
                                            @if (!empty($product_img->image))
                                                <img src="{{ asset('uploads/product/small/' . $product_img->image) }}"
                                                    alt="{{ $item->product->title }}" class="img-fluid"
                                                    style="max-width: 100px; max-height: 100px;">
                                            @else
                                                <img src="{{ asset('admin_assets/img/default-product.png') }}"
                                                    alt="{{ $item->product->title }}" class="img-fluid"
                                                    style="max-width: 100px; max-height: 100px;">
                                            @endif
                                        </a>

                                        <div class="pt-2">
                                            <h3 class="product-title fs-base mb-2">
                                                <a href="{{ route('frontend.product', $item->product->id) }}">
                                                    {{ $item->product->title }}
                                                </a>
                                            </h3>
                                            <div class="fs-lg text-accent pt-2">
                                                ${{ number_format($item->product->price, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="pt-2 ps-sm-3 mx-auto mx-sm-0 text-center">
                                        <button class="btn btn-outline-danger btn-sm remove-wishlist-item"
                                            data-id="{{ $item->id }}" type="button">
                                            <i class="fas fa-trash-alt me-2"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center">Your wishlist is empty.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(document).on('click', '.remove-wishlist-item', function() {
            var id = $(this).data('id');

            // Trigger SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user confirms, send the AJAX request to remove the item
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('frontend.remove_from_wishlist') }}', // Your defined route for removing wishlist items
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.status == true) {
                                // Show a success message
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                                // Optionally reload the page or remove the item from DOM
                                location
                            .reload(); // Or you can remove the item from the DOM without reloading
                            } else {
                                // Show an error message if something went wrong
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
