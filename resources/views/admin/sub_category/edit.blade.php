@extends('admin.layouts.app')


@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Update Sub-Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('sub_categories.list') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form id="subCategoryForm" name="subCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name">Category</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        @empty($record)
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $category->id == $sub_category->category_id ? 'selected' : '' }}>
                                                    {{ $category->name }}</option>
                                            @endforeach
                                        @endempty
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" value="{{ $sub_category->name }}"
                                        class="form-control" placeholder="Name">
                                    <p class="text-danger"></p> <!-- Placeholder for error message -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" value="{{ $sub_category->slug }}"
                                        class="form-control" placeholder="Slug" readonly>
                                    <p class="text-danger"></p> <!-- Placeholder for error message -->
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Show on home</label>
                                    <select name="is_home" id="is_home" class="form-control">
                                        <option value="1" {{ $sub_category->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $sub_category->status == 0 ? 'selected' : '' }}>Block
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ $sub_category->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $sub_category->status == 0 ? 'selected' : '' }}>Block
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('sub_categories.list') }}" class="btn btn-outline-dark ml-3">Cancel</a>
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
            $("#subCategoryForm").submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting normally

                var element = $(this);
                var submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true); // Disable the submit button

                $.ajax({
                    url: "{{ route('sub_categories.update', $sub_category->id) }}", // Dynamic route for updating
                    type: "PUT", // Use POST to allow _method for PUT
                    data: element.serialize(), // Serialize the form data (including _method)
                    dataType: "json",
                    success: function(response) {
                        // Handle success
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href =
                                        "{{ route('sub_categories.list') }}";
                                }
                            });
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
        });
    </script>
@endsection
