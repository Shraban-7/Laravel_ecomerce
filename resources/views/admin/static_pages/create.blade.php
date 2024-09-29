@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Page</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('pages.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <form action="" id="pageForm" name="pageForm">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
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
                                    <label for="email">Slug</label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                        placeholder="Slug" readonly>
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="content">Content</label>
                                    <textarea name="content" id="content" class="summernote" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Block</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('pages.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </div>
        </form>
        <!-- /.card -->
    </section>
@endsection

@section('js')
    <script>
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
        $("#pageForm").submit(function(e) {
            e.preventDefault();
            $("button[type='submit']").prop('disabled', true);


            $('#name').on('input', function() {
                $(this).removeClass('is-invalid');
                $(this).siblings('p.invalid-feedback').html(''); // Clear the error message
            });

            $.ajax({
                type: "post",
                url: "{{ route('pages.store') }}",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
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
                                    "{{ route('pages.index') }}"; // Redirect to user list
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
                        if (errors.slug) {
                            $("#slug").siblings('p').addClass('invalid-feedback').html(
                                errors.email[0]);
                            $("#slug").addClass('is-invalid');
                        }
                    } else {
                        console.log("An unexpected error occurred.");
                    }
                }
            });

        });
    </script>
@endsection
