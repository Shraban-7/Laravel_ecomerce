@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Brand</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('brands.list') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form id="brandForm" name="brandForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" value="{{ $brand->name }}"
                                        class="form-control" placeholder="Name">
                                    <p class="text-danger"></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" value="{{ $brand->slug }}"
                                        class="form-control" placeholder="Slug" readonly>
                                    <p class="text-danger"></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ $brand->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $brand->status == 0 ? 'selected' : '' }}>Block</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('brands.list') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
    </section>
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
                $('#slug').val(slug);

                // Clear slug field error when name field changes
                $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
            });

            // Clear validation error when typing in the name field
            $('#name').on('keyup change', function() {
                $(this).removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
            });

            // Handle form submission for brand update
            $("#brandForm").submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting normally

                var element = $(this);
                var submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true); // Disable the submit button

                $.ajax({
                    url: "{{ route('brands.update', $brand->id) }}", // Dynamic route for updating
                    type: "put",
                    data: element.serialize(),
                    dataType: "json",
                    success: function(response) {
                        $("#name, #slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('brands.list') }}";
                                }
                            });
                        } else if (response.status === 'warning') {
                            Swal.fire({
                                icon: 'info',
                                title: 'No Changes',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                            submitButton.prop('disabled', false);
                        }
                    },
                    error: function(jqXHR) {
                        var response = jqXHR.responseJSON;
                        if (response && response.errors) {
                            var errors = response.errors;

                            if (errors['name']) {
                                $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['name'][0]);
                            }

                            if (errors['slug'] && $('#name').val()) {
                                $("#slug").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['slug'][0]);
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'An error occurred while saving the brand. Please try again.',
                                confirmButtonText: 'OK'
                            });
                        }
                        submitButton.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
