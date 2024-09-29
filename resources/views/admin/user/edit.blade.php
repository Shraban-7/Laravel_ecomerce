@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit User</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form id="userForm" name="userForm">
                        @csrf
                        @method('PUT') <!-- Method spoofing for PUT request -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" placeholder="Name">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" placeholder="Email">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ $user->phone }}" placeholder="Phone">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
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
                    type: "PUT", // Using PUT method for updating
                    url: "{{ route('admin.users.update', $user->id) }}", // Replace with actual update route
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
                                    // Redirect to the users list page after successful update
                                    window.location.href = "{{ route('admin.users.index') }}";
                                }
                            });
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(jqXHR) {
                        // Enable the submit button again
                        $("button[type='submit']").prop('disabled', false);

                        if (jqXHR.status === 422) {
                            var errors = jqXHR.responseJSON.errors;

                            // Show validation errors for each field
                            if (errors.name) {
                                $("#name").addClass('is-invalid');
                                $("#name").siblings('p').html(errors.name[0]);
                            }
                            if (errors.email) {
                                $("#email").addClass('is-invalid');
                                $("#email").siblings('p').html(errors.email[0]);
                            }
                            if (errors.phone) {
                                $("#phone").addClass('is-invalid');
                                $("#phone").siblings('p').html(errors.phone[0]);
                            }
                            if (errors.password) {
                                $("#password").addClass('is-invalid');
                                $("#password").siblings('p').html(errors.password[0]);
                            }
                            if (errors.password_confirmation) {
                                $("#password_confirmation").addClass('is-invalid');
                                $("#password_confirmation").siblings('p').html(errors.password_confirmation[0]);
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
