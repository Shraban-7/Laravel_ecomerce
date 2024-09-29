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

    <section class="section-11">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-3">
                    @include('frontend.partials.account-pannel')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                        </div>
                        <form id="profileForm" name="profileForm">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name" value="{{ $user->name }}"
                                            placeholder="Enter Your Name" class="form-control">
                                        <p class="invalid-feedback" style="display: none;"></p>
                                        <!-- Placeholder for validation error -->
                                    </div>
                                    <div class="mb-3">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" id="email" value="{{ $user->email }}"
                                            placeholder="Enter Your Email" class="form-control">
                                        <p class="invalid-feedback" style="display: none;"></p>
                                        <!-- Placeholder for validation error -->
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone">Phone</label>
                                        <input type="text" name="phone" id="phone" value="{{ $user->phone }}"
                                            placeholder="Enter Your Phone" class="form-control">
                                        <p class="invalid-feedback" style="display: none;"></p>
                                        <!-- Placeholder for validation error -->
                                    </div>
                                    <div class="d-flex">
                                        <button type="submit" id="updateProfileBtn" class="btn btn-dark">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card mt-5">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Address Information</h2>
                        </div>
                        <form id="addressForm" name="addressForm">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="first_name">First Name</label>
                                        <input type="text" name="first_name" id="first_name"
                                            value="{{ $user_address->first_name ?? '' }}"
                                            placeholder="Enter Your First Name" class="form-control">
                                        <p class="invalid-feedback" style="display: none;"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" name="last_name" id="last_name"
                                            value="{{ $user_address->last_name ?? '' }}" placeholder="Enter Your Last Name"
                                            class="form-control">
                                        <p class="invalid-feedback" style="display: none;"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email_add"
                                            value="{{ $user_address->email ?? '' }}" placeholder="Enter Your Email"
                                            class="form-control">
                                        <p class="invalid-feedback" style="display: none;"></p>
                                        <!-- This will hold the error message -->
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone">Phone</label>
                                        <input type="text" name="phone" id="phone_add"
                                            value="{{ $user_address->phone ?? '' }}" placeholder="Enter Your Phone"
                                            class="form-control">
                                        <p class="invalid-feedback" style="display: none;"></p>
                                        <!-- This will hold the error message -->
                                    </div>

                                    <div class="mb-3">
                                        <label for="address">Address</label>
                                        <textarea name="address" id="address" class="form-control" placeholder="Enter Your Address">{{ $user_address->address ?? '' }}</textarea>
                                        <p class="invalid-feedback" style="display: none;"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="apartment">Apartment</label>
                                        <input type="text" name="apartment" id="apartment"
                                            value="{{ $user_address->apartment ?? '' }}"
                                            placeholder="Enter Your Apartment" class="form-control">
                                        <p class="invalid-feedback" style="display: none;"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="country">Country</label>
                                        <select name="country_id" id="country" class="form-control">
                                            <option value="" disabled selected>Select Your Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ isset($user_address) && $country->id == $user_address->country_id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="invalid-feedback" style="display: none;"></p>
                                        <!-- This will hold the error message -->
                                    </div>
                                    <div class="mb-3">
                                        <label for="city">City</label>
                                        <input type="text" name="city" id="city"
                                            value="{{ $user_address->city ?? '' }}" placeholder="Enter Your City"
                                            class="form-control">
                                        <p class="invalid-feedback" style="display: none;"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="state">State</label>
                                        <input type="text" name="state" id="state"
                                            value="{{ $user_address->state ?? '' }}" placeholder="Enter Your State"
                                            class="form-control">
                                        <p class="invalid-feedback" style="display: none;"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="zip">Zip Code</label>
                                        <input type="text" name="zip" id="zip"
                                            value="{{ $user_address->zip ?? '' }}" placeholder="Enter Your Zip Code"
                                            class="form-control">
                                        <p class="invalid-feedback" style="display: none;"></p>
                                    </div>
                                    <div class="d-flex">
                                        <button type="submit" id="updateProfileBtn" class="btn btn-dark">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Clear error messages on input
            $('#name, #email, #phone').on('input', function() {
                $(this).removeClass('is-invalid'); // Remove the invalid class
                $(this).siblings('.invalid-feedback').remove(); // Remove the error message
            });

            $('#profileForm').on('submit', function(e) {
                e.preventDefault();

                $("button[type='submit']").prop('disabled', true);

                // Clear previous error messages
                $('.invalid-feedback').remove(); // Remove existing error messages
                $('.is-invalid').removeClass('is-invalid'); // Remove invalid class from inputs

                $.ajax({
                    type: 'POST',
                    url: '{{ route('profile.update') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: $('#name').val(),
                        email: $('#email').val(),
                        phone: $('#phone').val(),
                    },
                    success: function(response) {
                        if (response.status == true) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                            $("button[type='submit']").prop('disabled', false);
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                            $("button[type='submit']").prop('disabled', false);
                        }
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status === 422) {
                            // Laravel validation error
                            var errors = jqXHR.responseJSON.errors;
                            $("button[type='submit']").prop('disabled', false);

                            // Handle Name Error
                            if (errors.name) {
                                $("#name").after('<p class="invalid-feedback">' + errors.name[
                                    0] + '</p>');
                                $("#name").addClass('is-invalid');
                            }

                            // Handle Email Error
                            if (errors.email) {
                                $("#email").after('<p class="invalid-feedback">' + errors.email[
                                    0] + '</p>');
                                $("#email").addClass('is-invalid');
                            }

                            // Handle Phone Error
                            if (errors.phone) {
                                $("#phone").after('<p class="invalid-feedback">' + errors.phone[
                                    0] + '</p>');
                                $("#phone").addClass('is-invalid');
                            }
                        } else {
                            $("button[type='submit']").prop('disabled', false);
                            console.log("An unexpected error occurred");
                        }
                    }
                });
            });


            $('#addressForm').on('submit', function(e) {
                e.preventDefault();

                $("button[type='submit']").prop('disabled', true);

                // Clear previous error messages
                $('.invalid-feedback').remove(); // Remove existing error messages
                $('.is-invalid').removeClass('is-invalid'); // Remove invalid class from inputs

                $.ajax({
                    type: 'POST',
                    url: '{{ route('update.address') }}', // Make sure to update this route
                    data: {
                        _token: '{{ csrf_token() }}',
                        first_name: $('#first_name').val(),
                        last_name: $('#last_name').val(),
                        email: $('#email_add').val(),
                        phone: $('#phone_add').val(),
                        address: $('#address').val(),
                        apartment: $('#apartment').val(),
                        country_id: $('#country').val(),
                        city: $('#city').val(),
                        state: $('#state').val(),
                        zip: $('#zip').val(),
                    },
                    success: function(response) {
                        if (response.status == true) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                            $("button[type='submit']").prop('disabled', false);
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                            $("button[type='submit']").prop('disabled', false);
                        }
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status === 422) {
                            // Laravel validation error
                            var errors = jqXHR.responseJSON.errors;
                            $("button[type='submit']").prop('disabled', false);

                            // Handle First Name Error
                            if (errors.first_name) {
                                $("#first_name").after('<p class="invalid-feedback">' + errors
                                    .first_name[0] + '</p>');
                                $("#first_name").addClass('is-invalid');
                            }

                            // Handle Last Name Error
                            if (errors.last_name) {
                                $("#last_name").after('<p class="invalid-feedback">' + errors
                                    .last_name[0] + '</p>');
                                $("#last_name").addClass('is-invalid');
                            }

                            // Handle Email Error
                            if (errors.email) {
                                $("#email_add").after('<p class="invalid-feedback">' + errors.email[
                                    0] + '</p>');
                                $("#email_add").addClass('is-invalid');
                            }

                            // Handle Phone Error
                            if (errors.phone) {
                                $("#phone_add").after('<p class="invalid-feedback">' + errors.phone[
                                    0] + '</p>');
                                $("#phone_add").addClass('is-invalid');
                            }

                            // Handle Address Error
                            if (errors.address) {
                                $("#address").after('<p class="invalid-feedback">' + errors
                                    .address[0] + '</p>');
                                $("#address").addClass('is-invalid');
                            }

                            // Handle Apartment Error (if applicable)
                            if (errors.apartment) {
                                $("#apartment").after('<p class="invalid-feedback">' + errors
                                    .apartment[0] + '</p>');
                                $("#apartment").addClass('is-invalid');
                            }

                            // Handle Country Error
                            if (errors.country_id) {
                                $("#country").after('<p class="invalid-feedback">' + errors
                                    .country_id[0] + '</p>');
                                $("#country").addClass('is-invalid');
                            }

                            // Handle City Error
                            if (errors.city) {
                                $("#city").after('<p class="invalid-feedback">' + errors.city[
                                    0] + '</p>');
                                $("#city").addClass('is-invalid');
                            }

                            // Handle State Error
                            if (errors.state) {
                                $("#state").after('<p class="invalid-feedback">' + errors.state[
                                    0] + '</p>');
                                $("#state").addClass('is-invalid');
                            }

                            // Handle Zip Code Error
                            if (errors.zip) {
                                $("#zip").after('<p class="invalid-feedback">' + errors.zip[0] +
                                    '</p>');
                                $("#zip").addClass('is-invalid');
                            }
                        } else {
                            $("button[type='submit']").prop('disabled', false);
                            console.log("An unexpected error occurred");
                        }
                    }
                });
            });

            // Clear error messages on input
            $('#first_name, #last_name, #email, #phone, #address, #apartment, #country, #city, #state, #zip').on(
                'input',
                function() {
                    $(this).removeClass('is-invalid'); // Remove the invalid class
                    $(this).siblings('.invalid-feedback').remove(); // Remove the error message
                });





        });
    </script>
@endsection
