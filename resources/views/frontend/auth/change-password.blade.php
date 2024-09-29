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
                            <h2 class="h5 mb-0 pt-2 pb-2">Change Password</h2>
                        </div>
                        <form id="changePasswordForm" method="POST">
                            @csrf
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="old_password">Old Password</label>
                                        <input type="password" name="old_password" id="old_password"
                                            placeholder="Old Password" class="form-control">
                                        <p class="invalid-feedback"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password">New Password</label>
                                        <input type="password" name="new_password" id="new_password"
                                            placeholder="New Password" class="form-control">
                                        <p class="invalid-feedback"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password_confirmation">Confirm Password</label>
                                        <input type="password" id="new_password_confirmation"
                                            name="new_password_confirmation" placeholder="Confirm Password"
                                            class="form-control">
                                        <p class="invalid-feedback"></p>
                                    </div>

                                    <div class="d-flex">
                                        <button class="btn btn-dark" type="submit">Save</button>
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
            $('#changePasswordForm').submit(function(e) {
                e.preventDefault(); // prevent the form from submitting traditionally

                // Disable the save button to prevent multiple submissions
                $("button[type='submit']").prop('disabled', true);

                let formData = {
                    old_password: $('#old_password').val(),
                    new_password: $('#new_password').val(),
                    new_password_confirmation: $('#new_password_confirmation').val(), // Updated
                    _token: "{{ csrf_token() }}" // for CSRF protection
                };

                $.ajax({
                    type: 'POST',
                    url: "{{ route('user.changePasswordSave') }}",
                    data: formData,
                    success: function(response) {
                        $("button[type='submit']").prop('disabled', false);

                        if (response.status==true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Password Updated!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Redirect to the login page after success
                                window.location.href = response.redirect;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(jqXHR) {
                        $("button[type='submit']").prop('disabled', false);

                        // Handle validation errors
                        let errors = jqXHR.responseJSON.errors;
                        if (errors.old_password) {
                            $('#old_password').addClass('is-invalid').siblings('p').html(errors
                                .old_password[0]);
                        }
                        if (errors.new_password) {
                            $('#new_password').addClass('is-invalid').siblings('p').html(errors
                                .new_password[0]);
                        }
                        if (errors.new_password_confirmation) { // Updated
                            $('#new_password_confirmation').addClass('is-invalid').siblings('p')
                                .html(errors.new_password_confirmation[0]);
                        }
                    }
                });
            });

            $('#old_password,#new_password,#new_password_confirmation').on('input', function() {
                $(this).removeClass('is-invalid'); // Remove the invalid class
                $(this).siblings('.invalid-feedback').html(''); // Clear the error message
            });
        });
    </script>
@endsection
