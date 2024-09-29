@extends('admin.layouts.app')


@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Change Password</h1>
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
            <form id="changePasswordForm" name="changePasswordForm" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="old_password">Old Password</label>
                                    <input type="password" name="old_password" id="old_password" placeholder="Old Password"
                                        class="form-control">
                                    <p class="invalid-feedback"></p> <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="new_password">New Password</label>
                                    <input type="password" name="new_password" id="new_password" placeholder="New Password"
                                        class="form-control">
                                    <p class="invalid-feedback"></p> <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <!-- Div acting as Dropzone -->
                                    <label for="new_password_confirmation">Confirm Password</label>
                                    <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                        placeholder="Confirm Password" class="form-control">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Save</button>

                </div>
            </form>

        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
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
                    url: "{{ route('admin.changePasswordSave') }}",
                    data: formData,
                    success: function(response) {
                        $("button[type='submit']").prop('disabled', false);

                        if (response.status == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Password Updated!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Redirect to the login page after success
                                window.location.reload();
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

                        // Check if old password does not match
                        if (jqXHR.status === 422 && jqXHR.responseJSON.message ===
                            'Old password does not match our records.') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Old Password Incorrect',
                                text: jqXHR.responseJSON.message
                            });
                        }

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
