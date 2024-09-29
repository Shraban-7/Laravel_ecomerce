@extends('frontend.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="#">Shop</a></li>
                    <li class="breadcrumb-item">Cart</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-9 pt-4">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table" id="cart">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($cart_content))
                                    @foreach ($cart_content as $item)
                                        @php
                                            $product_img = $item->options->productImage->image;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-around">
                                                    <img src="{{ asset('uploads/product/small/' . ($product_img ? $product_img : 'default.jpg')) }}"
                                                        width="" height="">
                                                    <h2>{{ $item->name }}</h2>
                                                </div>
                                            </td>
                                            <td>${{ $item->price }}</td>
                                            <td>
                                                <div class="input-group quantity mx-auto" style="width: 100px;">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-sm btn-dark btn-minus p-2 pt-1 pb-1 sub"
                                                            data-id="{{ $item->rowId }}">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text"
                                                        class="form-control form-control-sm  border-0 text-center"
                                                        value="{{ $item->qty }}">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-sm btn-dark btn-plus p-2 pt-1 pb-1 add"
                                                            data-id="{{ $item->rowId }}">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                ${{ $item->price * $item->qty }}
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="deleteCart('{{ $item->rowId }}');"><i
                                                        class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card cart-summery">

                        <div class="card-body">
                            <div class="sub-title">
                                <h2 class="bg-white">Cart Summery</h3>
                            </div>
                            <div class="d-flex justify-content-between pb-2">
                                <div>Subtotal</div>
                                <div>${{ Cart::subtotal() }}</div>
                            </div>
                            <div class="pt-2">
                                <a href="{{ route('frontend.checkout') }}" class="btn-dark btn btn-block w-100">Proceed to
                                    Checkout</a>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $('.add').click(function() {
            var qtyElement = $(this).parent().prev(); // Qty Input
            var qtyValue = parseInt(qtyElement.val());
            if (qtyValue < 10) {
                var rowId = $(this).data('id')
                qtyElement.val(qtyValue + 1);
                var newQty = qtyElement.val();
                updateCart(rowId, newQty)
            }
        });

        $('.sub').click(function() {
            var qtyElement = $(this).parent().next();
            var qtyValue = parseInt(qtyElement.val());
            if (qtyValue > 1) {
                var rowId = $(this).data('id')
                qtyElement.val(qtyValue - 1);
                var newQty = qtyElement.val();
                updateCart(rowId, newQty)
            }
        });

        function updateCart(rowId, qty) {
            $.ajax({
                type: "post",
                url: "{{ route('frontend.update_cart') }}",
                data: {
                    rowId: rowId,
                    qty: qty
                },
                dataType: "json",
                success: function(response) {
                    window.location.href = "{{ route('frontend.cart') }}"
                }
            });
        }

        function deleteCart(rowId) {
            if (confirm("Are you sure you want to remove this item from the cart?")) {
                $.ajax({
                    type: "post",
                    url: "{{ route('frontend.delete_cart') }}",
                    data: {
                        rowId: rowId,
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status == true) {
                            window.location.href = "{{ route('frontend.cart') }}";
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        }
    </script>
@endsection
