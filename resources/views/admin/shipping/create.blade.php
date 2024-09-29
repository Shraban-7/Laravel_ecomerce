@extends('admin.layouts.app')


@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Shipping Management</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('categories.list') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form id="shippingForm" name="shippingForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Shipping country</label>
                                    <select name="country_id" id="country_id" class="form-control">
                                        <option value="">Select a country</option>
                                        @if (!empty($countries))
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                        <option value="rest-of-world">Rest of world</option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Shipping charge</label>
                                    <input type="text" name="amount" id="amount" class="form-control"
                                        placeholder="Shiping charge">
                                    <p></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap table-striped">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if ($shippings->isNotEmpty())
                                @foreach ($shippings as $key => $shipping)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        @if (!empty($shipping->country->name))
                                            <td>{{ $shipping->country->name }}</td>
                                        @else
                                            <td>Rest of world</td>
                                        @endif
                                        <td>{{ $shipping->amount }}</td>

                                        <td>
                                            <a href="{{ route('shipping.edit', $shipping->id) }}">
                                                <svg class="filament-link-icon w-4 h-4 mr-1"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" aria-hidden="true">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <a href="#" data-id="{{ $shipping->id }}"
                                                class="text-danger delete-shipping w-4 h-4 mr-1">
                                                <svg class="filament-link-icon w-4 h-4 mr-1"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">Rcords Not Found</td>
                                </tr>
                            @endif


                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('js')
    <script>
        $("#shippingForm").submit(function(e) {
            e.preventDefault();

            // Disable the submit button to prevent multiple submissions
            $("button[type='submit']").prop('disabled', true);

            // Clear previous validation errors on form submit
            $(".is-invalid").removeClass('is-invalid');
            $(".invalid-feedback").html('');

            $.ajax({
                type: "post",
                url: "{{ route('shipping.store') }}", // Replace with your actual route
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    // Enable the submit button again
                    $("button[type='submit']").prop('disabled', false);

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Reload the page after user clicks 'OK'
                                window.location.reload();
                            }
                        });
                    }
                },
                error: function(jqXHR) {
                    // Enable the submit button again
                    $("button[type='submit']").prop('disabled', false);

                    if (jqXHR.status === 422) {
                        var errors = jqXHR.responseJSON.errors;

                        // Handle country_id error
                        if (errors.country_id) {
                            $("#country_id").siblings('p').addClass('invalid-feedback').html(errors
                                .country_id[0]);
                            $("#country_id").addClass('is-invalid');
                        }

                        // Handle amount error
                        if (errors.amount) {
                            $("#amount").siblings('p').addClass('invalid-feedback').html(errors.amount[
                                0]);
                            $("#amount").addClass('is-invalid');
                        }
                    } else {
                        console.log("An unexpected error occurred.");
                    }
                }
            });
        });

        // Remove validation error on input
        $('#country_id, #amount').on('input change', function() {
            $(this).removeClass('is-invalid');
            $(this).siblings('p').html(''); // Clear the error message
        });


        // delete shipping

        $(document).on('click', '.delete-shipping', function(e) {
            e.preventDefault();

            // Get the shipping ID from the data-id attribute
            var shippingId = $(this).data('id');

            // Confirm deletion with SweetAlert
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with AJAX request to delete
                    $.ajax({
                        type: 'DELETE',
                        url: "{{ route('shipping.destroy', ':id') }}".replace(':id',
                            shippingId), // Use route helper
                        data: {
                            _token: '{{ csrf_token() }}', // Pass the CSRF token
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );

                                // Optionally, remove the deleted row from the DOM
                                $('a[data-id="' + shippingId + '"]').closest('tr').remove();
                            }
                        },
                        error: function(jqXHR) {
                            Swal.fire(
                                'Error!',
                                'An error occurred while deleting the shipping charge.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endsection
