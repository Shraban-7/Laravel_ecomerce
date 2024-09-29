@extends('frontend.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="#">Shop</a></li>
                    <li class="breadcrumb-item">Checkout</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-9 pt-4">
        <div class="container">
            <form action="" id="orderForm" name="orderForm" method="post">
                <div class="row">
                    <div class="col-md-8">
                        <div class="sub-title">
                            <h2>Shipping Address</h2>
                        </div>
                        <div class="card shadow-lg border-0">
                            <div class="card-body checkout-form">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="first_name" id="first_name"
                                                value="{{ !empty($customerAddress) ? $customerAddress->first_name : '' }}"
                                                class="form-control" placeholder="First Name">
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="last_name" id="last_name"
                                                value="{{ !empty($customerAddress) ? $customerAddress->last_name : '' }}"
                                                class="form-control" placeholder="Last Name">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="email" id="email"
                                                value="{{ !empty($customerAddress) ? $customerAddress->email : '' }}"
                                                class="form-control" placeholder="Email">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <select name="country_id" id="country" class="form-control">
                                                <option value="">Select a Country</option>
                                                @if (!empty($countries))
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}"
                                                            {{ !empty($customerAddress) && $customerAddress->country_id == $country->id ? 'selected' : '' }}>
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <p></p> <!-- This p tag can be used for displaying validation error messages -->
                                        </div>
                                    </div>



                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <textarea name="address" id="address" cols="30" rows="3" placeholder="Address" class="form-control">{{ !empty($customerAddress) ? $customerAddress->first_name : '' }}</textarea>
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="apartment" id="apartment"
                                                value="{{ !empty($customerAddress) ? $customerAddress->apartment : '' }}"
                                                class="form-control" placeholder="Apartment, suite, unit, etc. (optional)">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="city" id="city"
                                                value="{{ !empty($customerAddress) ? $customerAddress->city : '' }}"
                                                class="form-control" placeholder="City">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="state" id="state"
                                                value="{{ !empty($customerAddress) ? $customerAddress->state : '' }}"
                                                class="form-control" placeholder="State">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="zip" id="zip"
                                                value="{{ !empty($customerAddress) ? $customerAddress->zip : '' }}"
                                                class="form-control" placeholder="Zip">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="phone" id="phone"
                                                value="{{ !empty($customerAddress) ? $customerAddress->phone : '' }}"
                                                class="form-control" placeholder="Mobile No.">
                                            <p></p>
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <textarea name="order_notes" id="order_notes" cols="30" rows="2" placeholder="Order Notes (optional)"
                                                class="form-control">{{ !empty($customerAddress) ? $customerAddress->first_name : '' }}</textarea>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">

                        <div class="sub-title">
                            <h2>Order Summery</h3>
                        </div>
                        <div class="card cart-summery">
                            <div class="card-body">
                                @foreach (Cart::content() as $item)
                                    <div class="d-flex justify-content-between pb-2">
                                        <div class="h6">{{ $item->name }}X{{ $item->qty }}</div>
                                        <div class="h6">${{ $item->price * $item->qty }}</div>
                                    </div>
                                @endforeach


                                <div class="d-flex justify-content-between summery-end">
                                    <div class="h6"><strong>Subtotal</strong></div>
                                    <div class="h6"><strong>${{ Cart::subtotal() }}</strong></div>
                                </div>
                                <div class="d-flex justify-content-between summery-end">
                                    <div class="h6"><strong>Discont</strong></div>
                                    <div class="h6"><strong id="discount_value">${{ $discount }}</strong></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <div class="h6"><strong>Shipping</strong></div>
                                    <div class="h6"><strong
                                            id="shippingAmount">${{ number_format($totalShippingCharge, 2) }}
                                        </strong></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2 summery-end">
                                    <div class="h5"><strong>Total</strong></div>
                                    <div class="h5"><strong
                                            id="grandTotal">${{ number_format($grandTotal, 2) }}</strong></div>
                                </div>
                            </div>
                        </div>

                        <div class="input-group apply-coupan mt-4">
                            <input type="text" placeholder="Coupon Code" class="form-control" name="discount_code"
                                id="discount_code">
                            <button class="btn btn-dark" type="button" id="apply-discount">Apply Coupon</button>
                        </div>

                        <div id="discount-response-wrapper">
                            {{-- @if (Session::has('code'))
                                <div class="mt-4 p-3 d-flex justify-content-between align-items-center bg-light border rounded"
                                    id="discount-response">
                                    <div>
                                        <strong class="text-success">Applied Coupon:
                                            {{ Session::get('code')->code }}</strong>
                                    </div>
                                    <a class="btn btn-sm btn-danger text-white shadow-sm" id="remove-dicount">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            @endif --}}
                        </div>


                        <div class="card payment-form ">
                            <h3 class="card-title h5 mb-3">Payment Method</h3>
                            <div class="">
                                <input checked type="radio" name="payment_method" value="cod" id="payment_1">
                                <label for="payment_1" class="form-check-label">COD</label>
                            </div>
                            <div class="">
                                <input type="radio" name="payment_method" value="cod" id="payment_2">
                                <label for="payment_2" class="form-check-label">Stripe</label>
                            </div>

                            <div class="card-body p-0 d-none mt-3" id="card_payment_form">
                                <div class="mb-3">
                                    <label for="card_number" class="mb-2">Card Number</label>
                                    <input type="text" name="card_number" id="card_number"
                                        placeholder="Valid Card Number" class="form-control">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="expiry_date" class="mb-2">Expiry Date</label>
                                        <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YYYY"
                                            class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="expiry_date" class="mb-2">CVV Code</label>
                                        <input type="text" name="expiry_date" id="expiry_date" placeholder="123"
                                            class="form-control">
                                    </div>
                                </div>

                            </div>
                            <div class="pt-4">
                                <button type="submit" class="btn-dark btn btn-block w-100">Pay Now</button>
                            </div>
                        </div>


                        <!-- CREDIT CARD FORM ENDS HERE -->

                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $('#payment_1').click(function() {
            if ($(this).is(":checked") == true) {
                $("#card_payment_form").addClass('d-none');
            }

        });
        $('#payment_2').click(function() {
            if ($(this).is(":checked") == true) {
                $("#card_payment_form").removeClass('d-none');
            }

        });


        $(document).ready(function() {
            // Handle form submission
            $("#orderForm").submit(function(e) {
                e.preventDefault();

                $('button[type="submit"]').prop('disabled', true);

                // Clear previous errors
                $(".is-invalid").removeClass("is-invalid");
                $(".invalid-feedback").html('');

                $.ajax({
                    type: "post",
                    url: "{{ route('frontend.process.checkout') }}",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        $('button[type="submit"]').prop('disabled', false);
                        if (response.status === true) {
                            // Redirect to thank you page
                            window.location.href =
                                `{{ route('frontend.thank_you', '') }}/${response.orderId}`;
                        }
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status === 422) {
                            // Laravel validation error
                            var errors = jqXHR.responseJSON.errors;

                            // Handle First Name Error
                            if (errors.first_name) {
                                $("#first_name").siblings('p').addClass('invalid-feedback')
                                    .html(errors.first_name[0]);
                                $("#first_name").addClass('is-invalid');
                            }

                            // Handle Last Name Error
                            if (errors.last_name) {
                                $("#last_name").siblings('p').addClass('invalid-feedback').html(
                                    errors.last_name[0]);
                                $("#last_name").addClass('is-invalid');
                            }

                            // Handle Email Error
                            if (errors.email) {
                                $("#email").siblings('p').addClass('invalid-feedback').html(
                                    errors.email[0]);
                                $("#email").addClass('is-invalid');
                            }

                            // Handle Country Error
                            if (errors.country_id) {
                                $("#country").siblings('p').addClass('invalid-feedback').html(
                                    errors.country_id[0]);
                                $("#country").addClass('is-invalid');
                            }

                            // Handle Address Error
                            if (errors.address) {
                                $("#address").siblings('p').addClass('invalid-feedback').html(
                                    errors.address[0]);
                                $("#address").addClass('is-invalid');
                            }

                            // Handle City Error
                            if (errors.city) {
                                $("#city").siblings('p').addClass('invalid-feedback').html(
                                    errors.city[0]);
                                $("#city").addClass('is-invalid');
                            }

                            // Handle State Error
                            if (errors.state) {
                                $("#state").siblings('p').addClass('invalid-feedback').html(
                                    errors.state[0]);
                                $("#state").addClass('is-invalid');
                            }

                            // Handle Zip Error
                            if (errors.zip) {
                                $("#zip").siblings('p').addClass('invalid-feedback').html(errors
                                    .zip[0]);
                                $("#zip").addClass('is-invalid');
                            }

                            // Handle Phone Error
                            if (errors.phone) {
                                $("#phone").siblings('p').addClass('invalid-feedback').html(
                                    errors.phone[0]);
                                $("#phone").addClass('is-invalid');
                            }
                        } else {
                            console.log("An unexpected error occurred");
                        }
                    }
                });
            });

            // Remove validation errors on input change
            $("input, select, textarea").on("input change", function() {
                $(this).removeClass('is-invalid'); // Remove the error class
                $(this).siblings('p').removeClass('invalid-feedback').html(''); // Clear error message
            });

            $("#country").change(function() {
                $.ajax({
                    type: "POST",
                    url: "{{ route('frontend.getOrderSummary') }}",
                    data: {
                        country_id: $(this).val(), // Send the country_id from the selected value
                        _token: "{{ csrf_token() }}" // Ensure you include the CSRF token for security
                    },
                    dataType: "json", // Expect JSON response from the server
                    success: function(response) {
                        if (response.status == true) {
                            $("#shippingAmount").html('$' + response.shippingCharge);
                            $("#grandTotal").html('$' + response.grandTotal);
                        }
                    },
                    error: function(xhr) {
                        console.log('An error occurred:', xhr.responseText);
                    }
                });
            });

            //coupon


            $('body').on('click', '#apply-discount', function() {
                // e.preventDefault();

                $.ajax({
                    type: "post",
                    url: "{{ route('frontend.applyDiscount') }}",
                    data: {
                        code: $('#discount_code').val(),
                        country_id: $('#country').val(),
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status == true) {
                            // Update the values with the applied discount
                            $("#shippingAmount").html('$' + response.shippingCharge);
                            $("#grandTotal").html('$' + response.grandTotal);
                            $("#discount_value").html('$' + response.discount);
                            $("#discount-response-wrapper").html(response
                                .discountString);
                            $('#discount_code').val('')

                        } else {
                            // Handle invalid coupon code
                            $("#discount-response-wrapper").html("<span class='text-danger py-3'>"+response.message+"</span>");
                        }
                    }
                });
            });


            $('body').on('click', '#remove-dicount', function() {
                $.ajax({
                    type: "post", // Corrected here
                    url: "{{ route('frontend.removeDiscount') }}",
                    data: {
                        country_id: $('#country').val(),
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status == true) {
                            $("#shippingAmount").html('$' + response.shippingCharge);
                            $("#grandTotal").html('$' + response.grandTotal);
                            $("#discount_value").html('$' + response.discount);
                            $("#discount-response").html('');
                        } else {
                            // Handle invalid coupon code
                            alert(response
                                .message
                            ); // You can replace this with a SweetAlert notification
                        }
                    }
                });
            })



        });
    </script>
@endsection
