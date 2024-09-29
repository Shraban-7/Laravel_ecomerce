@extends('admin.layouts.app')


@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Category</h1>
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
            <form id="categoryForm" name="categoryForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" value="{{ $category->name }}"
                                        class="form-control" placeholder="Name">
                                    <p class="text-danger"></p> <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" value="{{ $category->slug }}"
                                        class="form-control" placeholder="Slug" readonly>
                                    <p class="text-danger"></p> <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <!-- Div acting as Dropzone -->
                                    <div id="image" class="dropzone dz-clickable">
                                        <div class="dz-message needsclick">
                                            <br>Drop files here or click to upload.<br><br>
                                        </div>
                                    </div>
                                    <!-- Hidden input to store image ID -->
                                    <input type="hidden" id="image_id" name="image_id">
                                </div>
                                @if (!empty($category->image))
                                    <div>
                                        <img width="250"
                                            src="{{ asset('uploads/category/thumbnail/' . $category->image) }}"
                                            alt="" srcset="">
                                    </div>
                                @endif
                            </div>


                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Show on home</label>
                                    <select name="is_home" id="is_home" class="form-control">
                                        <option value="1" {{ $category->is_home == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $category->is_home == 0 ? 'selected' : '' }}>Block</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>Block</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('categories.list') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>

        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('js')
    <script>
        // Disable Dropzone auto-discovery globally before document is ready
        Dropzone.autoDiscover = false;

        $(document).ready(function() {
            // Auto-generate slug from the name field
            $('#name').on('keyup', function() {
                var name = $(this).val();
                var slug = name.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '') // Remove invalid characters
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/-+/g, '-'); // Replace multiple hyphens with a single hyphen
                $('#slug').val(slug); // Set the slug field

                // Clear slug field error when name field changes
                $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
            });

            // Clear validation error when typing in the name field
            $('#name').on('keyup change', function() {
                $(this).removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
            });

            // Handle form submission for category update
            $("#categoryForm").submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting normally

                var element = $(this);
                var submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true); // Disable the submit button

                $.ajax({
                    url: "{{ route('categories.update', $category->id) }}", // Dynamic route for updating
                    type: "put",
                    data: element.serialize(),
                    dataType: "json",
                    success: function(response) {
                        // Clear previous errors
                        $("#name, #slug").removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html("");

                        if (response.status === 'success') {
                            // SweetAlert success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Redirect to the categories list after success
                                    window.location.href =
                                        "{{ route('categories.list') }}";
                                }
                            });
                        } else if (response.status === 'warning') {
                            // Handle the case where no changes were detected
                            Swal.fire({
                                icon: 'info',
                                title: 'No Changes',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                            submitButton.prop('disabled', false); // Re-enable the submit button
                        }
                    },
                    error: function(jqXHR, exception) {
                        // Debugging logs
                        console.log('AJAX Error: ', jqXHR.status);
                        console.log('Response: ', jqXHR.responseText);

                        var response = jqXHR.responseJSON;

                        if (jqXHR.status === 302) {
                            var redirectUrl = jqXHR.getResponseHeader('X-Redirect-URL') ||
                                '/categories';

                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Category does not exist!',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href =
                                    redirectUrl; // Redirect to the categories list
                                }
                            });
                        } else {
                            // Handle validation errors or other server errors
                            if (response && response.errors) {
                                var errors = response.errors;

                                // Show validation errors for 'name'
                                if (errors['name']) {
                                    $("#name").addClass('is-invalid').siblings('p').addClass(
                                        'invalid-feedback').html(errors['name'][0]);
                                }

                                // Show validation errors for 'slug' if not read-only or auto-generated
                                if (errors['slug'] && $('#name').val()) {
                                    $("#slug").addClass('is-invalid').siblings('p').addClass(
                                        'invalid-feedback').html(errors['slug'][0]);
                                } else {
                                    $("#slug").removeClass('is-invalid').siblings('p')
                                        .removeClass('invalid-feedback').html('');
                                }

                                // SweetAlert error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'An error occurred while saving the category. Please try again.',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                console.log("Something went wrong", exception);
                            }
                            submitButton.prop('disabled', false); // Re-enable the submit button
                        }
                    }
                });
            });

            // Explicitly initialize Dropzone on the #image element
            const dropzone = new Dropzone("#image", {
                url: "{{ route('temp-images.create') }}", // Ensure this route is correct and returns the right response
                maxFiles: 1,
                paramName: 'image', // The name for the file upload field
                addRemoveLinks: true,
                acceptedFiles: "image/jpeg,image/png,image/gif",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                },
                init: function() {
                    // Ensure only one file can be uploaded at a time
                    this.on('addedfile', function(file) {
                        if (this.files.length > 1) {
                            this.removeFile(this.files[0]);
                        }
                    });

                    // Handle successful upload and store image ID in a hidden input
                    this.on('success', function(file, response) {
                        $("#image_id").val(response.image_id); // Assuming response has image_id
                    });

                    // Handle errors during upload
                    this.on('error', function(file, response) {
                        console.log(response);
                    });
                }
            });
        });
    </script>
@endsection
