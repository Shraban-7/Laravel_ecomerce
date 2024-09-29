@extends('admin.layouts.app')


@section('content')
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'OK'
                })
            });
        </script>
    @endif
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Categories</h1>
                </div>

            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <div class="card">
                <form action="" method="get">
                    <div class="card-header">
                        <div class="card-title">
                            <button type="button" onclick="window.location.href='{{ route('admin.orders') }}'"
                                class="btn btn-default btn-sm">Reset</button>
                        </div>
                        <form action="{{ route('admin.orders') }}" method="GET">
                            <div class="card-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="keyword" value="{{ Request::get('keyword') }}"
                                        class="form-control float-right" placeholder="Search">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </form>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Date Purchased</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.detail', $order->id) }}">
                                            OD_{{ $order->id }}
                                        </a>
                                    </td>
                                    <td>{{ $order->user->name }}</td> <!-- Assuming each order has a related user -->
                                    <td>{{ $order->user->email }}</td>
                                    <td>{{ $order->user->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                            class="badge
                                            @if ($order->status == 'delivered') bg-success
                                            @elseif($order->status == 'shipping') bg-info
                                            @elseif($order->status == 'canceled') bg-danger
                                            @else bg-warning @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>

                                    </td>

                                    <td>${{ number_format($order->grand_total, 2) }}</td>
                                    <!-- Assuming 'total' is a field -->
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination Links -->

                </div>
                <div class="card-footer clearfix">
                    {{ $orders->links() }}

                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('js')
    <script>
        $(document).on('click', '.delete-category', function(e) {
            e.preventDefault(); // Prevent the default anchor behavior

            var categoryId = $(this).data('id'); // Get the category ID
            var categoryRow = $(`#category-row-${categoryId}`);

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    categoryRow.remove();

                    $.ajax({
                        url: "{{ route('categories.destroy', ':id') }}".replace(':id',
                            categoryId), // Use named route and replace with categoryId
                        type: "DELETE",
                        data: {
                            "_token": "{{ csrf_token() }}" // Include the CSRF token
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', response.message, 'success').then(() => {
                                    window.location.href =
                                        "{{ route('categories.list') }}"; // Redirect on success
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                location.reload();
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            // Log the full response for debugging
                            if (jqXHR.status === 404) {
                                Swal.fire('Not Found!', 'The category was already deleted.',
                                    'error');
                            } else {
                                Swal.fire('Error!',
                                    'An error occurred while deleting the category.',
                                    'error');
                            }

                            location.reload();
                        }
                    });
                }
            });

            setInterval(function() {
                $.ajax({
                    url: "{{ route('categories.list') }}", // Route to get the updated list
                    type: "GET",
                    success: function(response) {
                        // Update your list UI with the fresh data
                        $('#category-list').html(
                            response); // Assuming the server returns the updated HTML
                    }
                });
            }, 30000); // 5 minutes (300,000 ms)

        });
    </script>
@endsection
