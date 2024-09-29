@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create User</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Back</a>
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
                <div class="card-body">
                    <form id="userForm" name="userForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Name">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="Email">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                        placeholder="Phone">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Password">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control" placeholder="Confirm Password">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                        </div>
                        <div class="pb-5 pt-3">
                            <button type="submit" class="btn btn-primary">Create</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </section>
    <!-- /.content -->
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#userForm').submit(function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Disable the submit button to prevent multiple submissions
                $("button[type='submit']").prop('disabled', true);

                // Clear previous validation errors on form submit
                $('#name, #email, #phone, #password, #password_confirmation').on('input', function() {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('p.invalid-feedback').html(''); // Clear the error message
                });

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.users.store') }}", // Your actual store route
                    data: $(this).serialize(), // Serialize form data
                    dataType: "json",
                    success: function(response) {
                        // Enable the submit button again
                        $("button[type='submit']").prop('disabled', false);

                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Reload the page after user clicks 'OK'
                                    window.location.href =
                                        "{{ route('admin.users.index') }}"; // Redirect to user list
                                }
                            });
                        } else {
                            // Handle any other status (if necessary)
                            alert(response.message);
                        }
                    },
                    error: function(jqXHR) {
                        // Enable the submit button again
                        $("button[type='submit']").prop('disabled', false);

                        if (jqXHR.status === 422) {
                            var errors = jqXHR.responseJSON.errors;

                            // Handle each field's error
                            if (errors.name) {
                                $("#name").siblings('p').addClass('invalid-feedback').html(
                                    errors.name[0]);
                                $("#name").addClass('is-invalid');
                            }
                            if (errors.email) {
                                $("#email").siblings('p').addClass('invalid-feedback').html(
                                    errors.email[0]);
                                $("#email").addClass('is-invalid');
                            }
                            if (errors.phone) {
                                $("#phone").siblings('p').addClass('invalid-feedback').html(
                                    errors.phone[0]);
                                $("#phone").addClass('is-invalid');
                            }
                            if (errors.password) {
                                $("#password").siblings('p').addClass('invalid-feedback').html(
                                    errors.password[0]);
                                $("#password").addClass('is-invalid');
                            }
                            if (errors.password_confirmation) {
                                $("#password_confirmation").siblings('p').addClass(
                                    'invalid-feedback').html(errors.password_confirmation[
                                    0]);
                                $("#password_confirmation").addClass('is-invalid');
                            }
                        } else {
                            console.log("An unexpected error occurred.");
                        }
                    }
                });
            });
        });
    </script>
@endsection
