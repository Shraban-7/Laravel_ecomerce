@extends('admin.layouts.app')


@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order: #{{ $order->id }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('admin.orders') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header pt-3">
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    <h1 class="h5 mb-3">Shipping Address</h1>
                                    <address>
                                        <strong>{{ $order->shippingAddress->name }}</strong><br>
                                        {{ $order->shippingAddress->address }}<br>
                                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}<br>
                                        {{ $order->shippingAddress->country->name }}<br> <!-- Display the country -->
                                        Phone: {{ $order->shippingAddress->phone }}<br>
                                        Email: {{ $order->user->email }}
                                    </address>

                                    <h6 class="heading-xxxs text-muted">Shipped date:</h6>
                                    <p class="mb-lg-0 fs-sm fw-bold">
                                        <time
                                            datetime="{{ $order->shipped_date ? $order->shipped_date->toDateTimeString() : null }}">
                                            {{ $order->shipped_date ? $order->shipped_date->format('d M, Y H:i') : 'Not shipped yet' }}
                                        </time>
                                    </p>
                                </div>

                                <div class="col-sm-4 invoice-col">
                                    <b>Invoice #{{ $order->id }}</b><br>
                                    <b>Order ID:</b> {{ $order->id }}<br>
                                    <b>Total:</b> ${{ number_format($order->grand_total, 2) }}<br>
                                    <b>Status:</b>
                                    <span
                                        class="text-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($order->status) }}
                                    </span><br>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-3">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th width="100">Price</th>
                                        <th width="100">Qty</th>
                                        <th width="100">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderItems as $item)
                                        <tr>
                                            <td>{{ $item->product->title }}</td>
                                            <td>${{ number_format($item->price, 2) }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>${{ number_format($item->price * $item->qty, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="3" class="text-right">Subtotal:</th>
                                        <td>${{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Shipping:</th>
                                        <td>${{ number_format($order->shipping, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Discount:</th>
                                        <td>${{ number_format($order->discount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Grand Total:</th>
                                        <td>${{ number_format($order->grand_total, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Order Status</h2>
                            <form id="updateStatusForm" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="status">Order Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>
                                            Shipping
                                        </option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>
                                            Delivered</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="shipped_date">Order Shipping Date</label>
                                    <input type="datetime-local" name="shipped_date" id="shipped_date" class="form-control"
                                        value="{{ $order->shipped_date ? \Carbon\Carbon::parse($order->shipped_date)->format('Y-m-d\TH:i') : '' }}">
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Send Invoice Email</h2>
                            <form id="sendInvoiceForm" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <select name="recipient" id="recipient" class="form-control">
                                        <option value="customer">Customer</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#updateStatusForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission
                $("button[type='submit']").prop('disabled', true); // Disable button while processing
                $.ajax({
                    url: "{{ route('admin.orders.update', $order->id) }}", // Update route
                    type: 'POST',
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        $("button[type='submit']").prop('disabled', false); // Re-enable button
                        // Show success message with SweetAlert
                        if (response.status === true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location
                                        .reload(); // Reload page on confirmation
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        $("button[type='submit']").prop('disabled', false); // Re-enable button

                        // Check if there are validation errors
                        if (xhr.status === 422) { // Unprocessable Entity
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = '';
                            for (let field in errors) {
                                errorMessages += errors[field].join(' ') +
                                    '\n'; // Join error messages for each field
                            }
                            // Show validation errors with SweetAlert
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMessages,
                                confirmButtonText: 'OK'
                            });
                        } else {
                            // Handle generic error
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'An error occurred while updating the order. Please try again.',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });

            $('#sendInvoiceForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            $("button[type='submit']").prop('disabled', true); // Disable the button to prevent multiple clicks

            $.ajax({
                url: "{{ route('admin.orders.sendInvoice', $order->id) }}", // Replace with your route
                type: 'POST',
                data: $(this).serialize(), // Serialize form data
                success: function(response) {
                    $("button[type='submit']").prop('disabled', false); // Re-enable button
                    // Show success message with SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                },
                error: function(xhr) {
                    $("button[type='submit']").prop('disabled', false); // Re-enable button
                    // Handle error here
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: xhr.responseJSON.error || 'An error occurred while sending the email. Please try again.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
        });
    </script>
@endsection
