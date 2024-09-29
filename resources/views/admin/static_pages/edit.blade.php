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

        {{-- @csrf
            @method('PUT') --}}
        <div class="container-fluid">
            <form id="pageUpdateForm" name="pageUpdateForm">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $page->name) }}" class="form-control" placeholder="Name">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Slug</label>
                                    <input type="text" name="slug" id="slug"
                                        value="{{ old('slug', $page->slug) }}" class="form-control" placeholder="Slug"
                                        readonly>
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="content">Content</label>
                                    <textarea name="content" id="content" class="summernote" cols="30" rows="10">
                                        {{ old('content', $page->content) }}
                                    </textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{ $page->status == 1 ? 'selected' : '' }} value="1">Active</option>
                                        <option {{ $page->status == 0 ? 'selected' : '' }} value="0">Block</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('pages.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
@endsection

@section('js')
    <script>
        $("#pageUpdateForm").submit(function(e) {
            e.preventDefault();
            $("button[type='submit']").prop('disabled', true);

            $.ajax({
                type: "PUT", // Use PUT for update
                url: "{{ route('pages.update', $page->id) }}",
                data: {
                    name: $('#name').val(),
                    slug: $('#slug').val(),
                    content: $('#content').summernote('code'),
                    status: $('#status').val()
                },
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
                                window.location.href = "{{ route('pages.index') }}";
                            }
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function(jqXHR) {
                    $("button[type='submit']").prop('disabled', false);

                    if (jqXHR.status === 422) {
                        var errors = jqXHR.responseJSON.errors;

                        if (errors.name) {
                            $("#name").siblings('p').addClass('invalid-feedback').html(errors.name[0]);
                            $("#name").addClass('is-invalid');
                        }
                        if (errors.slug) {
                            $("#slug").siblings('p').addClass('invalid-feedback').html(errors.slug[0]);
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
