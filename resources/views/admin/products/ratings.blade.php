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
                    <h1>Products</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.create') }}" class="btn btn-primary">New Product</a>
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
                            <button type="button" onclick="window.location.href='{{ route('categories.list') }}'"
                                class="btn btn-default btn-sm">Reset</button>
                        </div>
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
                    </div>
                </form>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>User name</th>
                                <th width="100">Status</th>
                                {{-- <th width="100">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @if ($ratings->isNotEmpty())
                                @foreach ($ratings as $key => $rating)
                                    <tr>
                                        <td>{{ ++$key }}</td>

                                        <td><a href="#">{{ $rating->product->title }}</a></td>
                                        <td>{{ $rating->rating }}</td>
                                        <td>{{ $rating->comment }}</td>
                                        <td>{{ $rating->username }}</td>
                                        <td>
                                            <input type="checkbox" class="status-toggle" data-size="small"
                                                data-id="{{ $rating->id }}" {{ $rating->status === 1 ? 'checked' : '' }}
                                                data-toggle="toggle" data-on="Active" data-off="Inactive"
                                                data-onstyle="success" data-offstyle="danger">
                                        </td>
                                        {{-- <td>

                                            <a href="#" data-id="{{ $rating->id }}"
                                                class="text-danger delete-product">
                                                <svg class="filament-link-icon w-4 h-4 mr-1"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </a>

                                        </td> --}}



                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">Rcords Not Found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $ratings->links() }}
                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('js')
    <script>
        // $(document).on('click', '.delete-product', function(e) {
        //     e.preventDefault(); // Prevent the default anchor behavior

        //     var productId = $(this).data('id'); // Get the product ID
        //     var productRow = $(`#product-row-${productId}`);

        //     Swal.fire({
        //         title: 'Are you sure?',
        //         text: "You won't be able to revert this!",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: 'Yes, delete it!'
        //     }).then((result) => {
        //         if (result.isConfirmed) {

        //             $.ajax({
        //                 url: "{{ route('products.destroy', ':id') }}".replace(':id',
        //                     productId), // Use named route and replace with productId
        //                 type: "DELETE",
        //                 data: {
        //                     "_token": "{{ csrf_token() }}" // Include the CSRF token
        //                 },
        //                 success: function(response) {
        //                     if (response.status === 'success') {
        //                         // Remove the product row from the table after successful deletion
        //                         productRow.remove();
        //                         Swal.fire('Deleted!', response.message, 'success').then(() => {
        //                             window.location.href =
        //                                 "{{ route('products.list') }}"; // Optional: redirect to product list
        //                         });
        //                     } else {
        //                         Swal.fire('Error!', response.message, 'error');
        //                     }
        //                 },
        //                 error: function(jqXHR, textStatus, errorThrown) {
        //                     // Handle error scenarios
        //                     if (jqXHR.status === 404) {
        //                         Swal.fire('Not Found!', 'The product was already deleted.',
        //                             'error');
        //                     } else {
        //                         Swal.fire('Error!',
        //                             'An error occurred while deleting the product.', 'error'
        //                         );
        //                     }
        //                 }
        //             });
        //         }
        //     });
        // });

        $(document).on('change', '.status-toggle', function(e) {
            e.preventDefault();

            var ratingId = $(this).data('id'); // Get the rating ID
            var newStatus = $(this).is(':checked') ? 1 : 0; // Get the new status (1 if checked, 0 if unchecked)

            $.ajax({
                url: "{{ route('product-ratings.update-status', ':id') }}".replace(':id', ratingId),
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "status": newStatus
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Updated!', response.message, 'success');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire('Error!', 'An error occurred while updating the status.', 'error');
                }
            });
        });
    </script>
@endsection
