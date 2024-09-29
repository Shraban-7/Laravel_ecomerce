@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Shipping Details</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('categories.list') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form id="shippingEditForm" name="shippingEditForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Shipping Country</label>
                                    <select name="country_id" id="country_id" class="form-control">
                                        <option value="">Select a country</option>

                                        @if (!empty($countries))
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ $shipping->country_id == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        @endif

                                        <option value="rest-of-world"
                                            {{ $shipping->country_id == 'rest-of-world' ? 'selected' : '' }}>
                                            Rest of world
                                        </option>
                                    </select>

                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount">Shipping Charge</label>
                                    <input type="text" name="amount" id="amount" class="form-control"
                                        value="{{ $shipping->amount }}" placeholder="Shipping charge">
                                    <p></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $("#shippingEditForm").submit(function(e) {
            e.preventDefault();

            // Disable the submit button to prevent multiple submissions
            $("button[type='submit']").prop('disabled', true);

            // Clear previous validation errors on form submit
            $(".is-invalid").removeClass('is-invalid');
            $(".invalid-feedback").html('');

            $.ajax({
                type: "post",
                url: "{{ route('shipping.update', $shipping->id) }}", // Replace with your actual route
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
                                window.location.href = "{{ route('shipping.create') }}";
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
    </script>
@endsection
