@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Discount Coupon</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('discount_coupons.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form id="couponForm" name="couponForm">
                @csrf
                @method('PUT') <!-- Specify the PUT method for update -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code">Coupon Code</label>
                                    <input type="text" name="code" id="code" class="form-control"
                                        value="{{ $coupon->code }}" placeholder="Coupon Code">
                                    <p></p> <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ $coupon->name }}" placeholder="Name">
                                    <p></p> <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control" placeholder="Description">{{ $coupon->description }}</textarea>
                                    <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_uses">Max Uses</label>
                                    <input type="number" name="max_uses" id="max_uses" class="form-control"
                                        value="{{ $coupon->max_uses }}" placeholder="Max Uses">
                                    <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_uses_user">Max Uses Per User</label>
                                    <input type="number" name="max_uses_user" id="max_uses_user" class="form-control"
                                        value="{{ $coupon->max_uses_user }}" placeholder="Max Uses Per User">
                                    <p class="text-danger"></p> <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type">Discount Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>Fixed Amount
                                        </option>
                                        <option value="percentage" {{ $coupon->type == 'percentage' ? 'selected' : '' }}>
                                            Percentage</option>
                                    </select>
                                    <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discount_amount">Discount Amount</label>
                                    <input type="number" name="discount_amount" id="discount_amount" class="form-control"
                                        value="{{ $coupon->discount_amount }}" placeholder="Discount Amount">
                                    <p></p> <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_amount">Minimum Amount</label>
                                    <input type="number" name="min_amount" id="min_amount" class="form-control"
                                        value="{{ $coupon->min_amount }}" placeholder="Minimum Amount">
                                    <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_at">Start Date</label>
                                    <input type="datetime-local" name="start_at" id="start_at" class="form-control"
                                        value="{{ old('start_at', $start_at) }}">
                                    <!-- Placeholder for error message -->
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expires_at">Expires At</label>
                                    <input type="datetime-local" name="expires_at" id="expires_at" class="form-control"
                                        value="{{ old('expires_at', $expires_at) }}">
                                    <!-- Placeholder for error message -->
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ $coupon->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $coupon->status == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('discount_coupons.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('js')
    <script>
        // Clear validation errors on input change
        $('#code, #name, #discount_amount, #start_at, #expires_at').on('input change', function() {
            $(this).removeClass('is-invalid');
            $(this).siblings('p').html(''); // Clear the specific error message
        });

        $("#couponForm").submit(function(e) {
            e.preventDefault(); // Prevent form from submitting normally

            // Disable the submit button to prevent multiple submissions
            $("button[type='submit']").prop('disabled', true);

            // Clear previous validation errors on form submit
            $(".is-invalid").removeClass('is-invalid');
            $(".invalid-feedback").html('');

            $.ajax({
                url: "{{ route('discount_coupons.update', $coupon->id) }}", // Update the URL to your update route
                type: "post",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    // Enable the submit button again
                    $("button[type='submit']").prop('disabled', false);

                    if (response.status === 'success') {
                        // SweetAlert success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to the discount coupons list route
                                window.location.href = "{{ route('discount_coupons.index') }}";
                            }
                        });
                    }
                },
                error: function(jqXHR) {
                    // Enable the submit button again
                    $("button[type='submit']").prop('disabled', false);

                    if (jqXHR.status === 422) {
                        var errors = jqXHR.responseJSON.errors;

                        // Handle code error
                        if (errors.code) {
                            $("#code").siblings('p').addClass('invalid-feedback').html(errors.code[0]);
                            $("#code").addClass('is-invalid'); // Display error message
                        }

                        // Handle name error
                        if (errors.name) {
                            $("#name").siblings('p').addClass('invalid-feedback').html(errors.name[0]);
                            $("#name").addClass('is-invalid');
                        }

                        // Handle discount_amount error
                        if (errors.discount_amount) {
                            $("#discount_amount").siblings('p').addClass('invalid-feedback').html(errors
                                .discount_amount[0]);
                            $("#discount_amount").addClass('is-invalid');
                        }

                        // Handle start_at error
                        if (errors.start_at) {
                            $("#start_at").addClass('is-invalid');
                            $("#start_at").siblings('p').addClass('invalid-feedback').html(errors
                                .start_at[0]);
                        }

                        // Handle expires_at error
                        if (errors.expires_at) {
                            $("#expires_at").addClass('is-invalid');
                            $("#expires_at").siblings('p').addClass('invalid-feedback').html(errors
                                .expires_at[0]);
                        }

                        // SweetAlert error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'An error occurred while updating the coupon. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        console.log("An unexpected error occurred.");
                    }
                }
            });
        });
    </script>
@endsection
