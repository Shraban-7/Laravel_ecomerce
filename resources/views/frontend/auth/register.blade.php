@extends('frontend.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item">Register</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
            <div class="login-form">
                <form action="" method="post" name="registrationForm" id="registrationForm">
                    <h4 class="modal-title">Register Now</h4>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Name" id="name" name="name">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Email" id="email" name="email">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Phone" id="phone" name="phone">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Confirm Password"
                            id="password_confirmation" name="password_confirmation">
                        <p></p>
                    </div>
                    <div class="form-group small">
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-dark btn-block btn-lg" value="Register">Register</button>
                </form>
                <div class="text-center small">Already have an account? <a href="{{ route('login') }}">Login Now</a></div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $("#registrationForm").submit(function(e) {
            e.preventDefault();

            // Clear previous error messages
            $('input').siblings('p').removeClass('invalid-feedback').html('');
            $('input').removeClass('is-invalid');

            $.ajax({
                type: "post",
                url: "{{ route('register.save') }}",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    window.location.href = "{{ route('login') }}"
                },
                error: function(jqXHR) {
                    if (jqXHR.status === 422) {
                        // Laravel validation error
                        var errors = jqXHR.responseJSON.errors;

                        // Handle Name Error
                        if (errors.name) {
                            $("#name").siblings('p').addClass('invalid-feedback').html(errors.name[0]);
                            $("#name").addClass('is-invalid');
                        }

                        // Handle Email Error
                        if (errors.email) {
                            $("#email").siblings('p').addClass('invalid-feedback').html(errors.email[
                            0]);
                            $("#email").addClass('is-invalid');
                        }

                        // Handle Phone Error
                        if (errors.phone) {
                            $("#phone").siblings('p').addClass('invalid-feedback').html(errors.phone[
                            0]);
                            $("#phone").addClass('is-invalid');
                        }

                        // Handle Password Error
                        if (errors.password) {
                            $("#password").siblings('p').addClass('invalid-feedback').html(errors
                                .password[0]);
                            $("#password").addClass('is-invalid');
                        }

                        // Handle Password Confirmation Error
                        if (errors.password_confirmation) {
                            $("#password_confirmation").siblings('p').addClass('invalid-feedback').html(
                                errors.password_confirmation[0]);
                            $("#password_confirmation").addClass('is-invalid');
                        }
                    } else {
                        console.log("An unexpected error occurred");
                    }
                }
            });
        });
    </script>
@endsection
