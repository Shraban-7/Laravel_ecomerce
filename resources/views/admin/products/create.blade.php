@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Product</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="products.html" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="post" name="productForm" id="productForm">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="title">Title</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                placeholder="Title">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="title">Slug</label>
                                            <input type="text" name="slug" id="slug" readonly
                                                class="form-control" placeholder="Slug">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" cols="30" rows="10" class="summernote"
                                                placeholder="Description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description">Short Description</label>
                                            <textarea name="short_description" id="short_description" cols="30" rows="10" class="summernote"
                                                placeholder=""></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description">Shipping & Returns</label>
                                            <textarea name="shipping_returns" id="shipping_returns" cols="30" rows="10" class="summernote" placeholder=""></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Media</h2>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <br>Drop files here or click to upload.<br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3" id="product-gallery">

                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Pricing</h2>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="price">Price</label>
                                            <input type="text" name="price" id="price" class="form-control"
                                                placeholder="Price">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="compare_price">Compare at Price</label>
                                            <input type="text" name="compare_price" id="compare_price"
                                                class="form-control" placeholder="Compare Price">
                                            <p class="text-muted mt-3">
                                                To show a reduced price, move the product’s original price into Compare at
                                                price. Enter a lower value into Price.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Inventory</h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku">SKU (Stock Keeping Unit)</label>
                                            <input type="text" name="sku" id="sku" class="form-control"
                                                placeholder="sku">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="barcode">Barcode</label>
                                            <input type="text" name="barcode" id="barcode" class="form-control"
                                                placeholder="Barcode">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="track_qty" value="0">
                                                <input class="custom-control-input" type="checkbox" id="track_qty"
                                                    name="track_qty" value="1" checked>
                                                <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <input type="number" min="0" name="qty" id="qty"
                                                class="form-control" placeholder="Qty">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product status</h2>
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Block</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h2 class="h4  mb-3">Product category</h2>
                                <div class="mb-3">
                                    <label for="category">Category</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="">Select Sub Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="category">Sub category</label>
                                    <select name="sub_category_id" id="sub_category_id" class="form-control">
                                        <option value="">Select Sub Category</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product brand</h2>
                                <div class="mb-3">
                                    <select name="brand_id" id="brand_id" class="form-control">

                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Featured product</h2>
                                <div class="mb-3">
                                    <select name="is_featured" id="is_featured" class="form-control">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Related product</h2>
                                <div class="mb-3">
                                    <select multiple name="related_products[]" id="related_products" class="form-control">

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('products.list') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection


@section('js')
    <script>
        Dropzone.autoDiscover = false;
        $(document).ready(function() {
            $('#related_products').select2({
                theme: "classic",
                ajax: {
                    url: '{{ route('related_product') }}',
                    dataType: 'json',
                    delay: 250,
                    minimumInputLength: 3,
                    multiple: true,
                    tags: true,
                    processResults: function(data) {
                        return {
                            results: data.tags
                        };
                    },

                },
            });
            // Auto-generate slug from the name field
            $('#title').on('keyup', function() {
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
            $('#title').on('keyup change', function() {
                $(this).removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
            });


            // Handle form submission
            $("#productForm").submit(function(e) {
                e.preventDefault(); // Prevent form from submitting normally

                var element = $(this);
                var submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true); // Disable the submit button

                $.ajax({
                    url: "{{ route('products.save') }}", // Update to the appropriate product save route
                    type: "post",
                    data: element.serialize(),
                    dataType: "json",
                    success: function(response) {
                        // Clear previous errors
                        $("#title, #slug, #description, #price, #compare_price, #sku, #barcode, #qty")
                            .removeClass('is-invalid').siblings('p').removeClass(
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
                                    // Redirect to the products list route
                                    window.location.href =
                                        "{{ route('products.list') }}";
                                }
                            });
                        }
                    },
                    error: function(jqXHR, exception) {
                        var response = jqXHR.responseJSON;
                        if (response && response.errors) {
                            var errors = response.errors;

                            // Show validation errors for 'title'
                            if (errors['title']) {
                                $("#title").addClass('is-invalid')
                                    .siblings('p').addClass('invalid-feedback').html(errors[
                                        'title'][0]);
                            }

                            // Show validation errors for 'slug' if not read-only or auto-generated
                            if (errors['slug'] && $('#title').val()) {
                                $("#slug").addClass('is-invalid')
                                    .siblings('p').addClass('invalid-feedback').html(errors[
                                        'slug'][0]);
                            } else {
                                $("#slug").removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html('');
                            }

                            // Show validation errors for 'description'
                            if (errors['description']) {
                                $("#description").addClass('is-invalid')
                                    .siblings('p').addClass('invalid-feedback').html(errors[
                                        'description'][0]);
                            }

                            // Show validation errors for 'price'
                            if (errors['price']) {
                                $("#price").addClass('is-invalid')
                                    .siblings('p').addClass('invalid-feedback').html(errors[
                                        'price'][0]);
                            }

                            // Show validation errors for 'compare_price'
                            if (errors['compare_price']) {
                                $("#compare_price").addClass('is-invalid')
                                    .siblings('p').addClass('invalid-feedback').html(errors[
                                        'compare_price'][0]);
                            }

                            // Show validation errors for 'sku'
                            if (errors['sku']) {
                                $("#sku").addClass('is-invalid')
                                    .siblings('p').addClass('invalid-feedback').html(errors[
                                        'sku'][0]);
                            }

                            // Show validation errors for 'barcode'
                            if (errors['barcode']) {
                                $("#barcode").addClass('is-invalid')
                                    .siblings('p').addClass('invalid-feedback').html(errors[
                                        'barcode'][0]);
                            }

                            // Show validation errors for 'qty'
                            if (errors['qty']) {
                                $("#qty").addClass('is-invalid')
                                    .siblings('p').addClass('invalid-feedback').html(errors[
                                        'qty'][0]);
                            }

                            // SweetAlert error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'An error occurred while saving the product. Please try again.',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    submitButton.prop('disabled',
                                        false); // Re-enable the submit button
                                }
                            });
                        } else {
                            console.log("Something went wrong", exception);
                        }
                    }
                });
            });



            $("#category_id").change(function() {

                var category_id = $(this).val();
                $.ajax({
                    type: "get",
                    url: "{{ route('products.sub_category') }}",
                    data: {
                        category_id: category_id
                    },
                    dataType: "json",
                    success: function(response) {
                        // console.log(response);

                        $("#sub_category_id").find("option").not(":first").remove();
                        $.each(response["sub_categories"], function(key, value) {
                            $("#sub_category_id").append(
                                `<option value="${ value.id }">${value.name}</option>`
                            );
                        });
                    }
                });

            });




            // Explicitly initialize Dropzone on the #image element
            const dropzone = new Dropzone("#image", {
                url: "{{ route('temp-images.create') }}", // Ensure this route is correct and returns the right response
                maxFiles: 10,
                paramName: 'image', // The name for the file upload field
                addRemoveLinks: true,
                acceptedFiles: "image/jpeg,image/png,image/gif",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                },
                success: function(file, response) {

                    var html = `<div class="col-md-3 m-3" id="image-row-${response.image_id}">
                                    <div class="card">
                                        <input type="hidden" name="image_array[]" value="${response.image_id}">
                                        <div class="ratio ratio-4x3">
                                            <img src="${response.image_path}" class="card-img-top img-fluid" style="width: 300px; height: 275px; object-fit: cover;" alt="...">
                                        </div>
                                        <div class="card-body">
                                            <a href="javascript:void(0)" onclick="deleteImage(${response.image_id})" class="btn btn-danger w-100">Delete</a>
                                        </div>
                                    </div>
                                </div>`;

                    $("#product-gallery").append(html);
                },
                complete: function(file) {
                    this.removeFile(file);
                }
            });
        });

        function deleteImage(id) {
            $("#image-row-" + id).remove();
        }
    </script>
@endsection
