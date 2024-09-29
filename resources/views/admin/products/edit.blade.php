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
                    <a href="{{ route('products.list') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" name="productForm" id="productForm">

                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="title">Title</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                value="{{ old('title', $product->title) }}" placeholder="Title">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="slug">Slug</label>
                                            <input type="text" name="slug" id="slug" class="form-control"
                                                value="{{ old('slug', $product->slug) }}" readonly placeholder="Slug">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" cols="30" rows="10" class="summernote"
                                                placeholder="Description">{{ old('description', $product->description) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description">Short Description</label>
                                            <textarea name="short_description" id="short_description" cols="30" rows="10" class="summernote"
                                                placeholder="">{{ old('short_description', $product->short_description) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description">Shipping & Returns</label>
                                            <textarea name="shipping_returns" id="shipping_returns" cols="30" rows="10" class="summernote" placeholder="">{{ old('shipping_returns', $product->shipping_returns) }}</textarea>
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
                            @isset($product_images)
                                @forelse ($product_images as $image)
                                    <div class="col-md-3 m-3" id="image-row-{{ $image->id }}">
                                        <div class="card">
                                            <input type="hidden" name="image_array[]" value="{{ $image->id }}">
                                            <div class="ratio ratio-4x3">
                                                <img src="{{ asset('uploads/product/small/' . $image->image) }}"
                                                    class="card-img-top img-fluid"
                                                    style="width: 300px; height: 275px; object-fit: cover;" alt="...">
                                            </div>
                                            <div class="card-body">
                                                <a href="javascript:void(0)" onclick="deleteImage({{ $image->id }})"
                                                    class="btn btn-danger w-100">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p>No images available.</p>
                                @endforelse
                            @endisset
                        </div>


                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Pricing</h2>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="price">Price</label>
                                            <input type="text" name="price" id="price" class="form-control"
                                                value="{{ old('price', $product->price) }}" placeholder="Price">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="compare_price">Compare at Price</label>
                                            <input type="text" name="compare_price" id="compare_price"
                                                class="form-control"
                                                value="{{ old('compare_price', $product->compare_price) }}"
                                                placeholder="Compare Price">
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
                                                value="{{ old('sku', $product->sku) }}" placeholder="SKU">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="barcode">Barcode</label>
                                            <input type="text" name="barcode" id="barcode" class="form-control"
                                                value="{{ old('barcode', $product->barcode) }}" placeholder="Barcode">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="track_qty" value="0">
                                                <input class="custom-control-input" type="checkbox" id="track_qty"
                                                    name="track_qty" value="1"
                                                    {{ old('track_qty', $product->track_qty) == 1 ? 'checked' : '' }}>
                                                <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <input type="number" min="0" name="qty" id="qty"
                                                class="form-control" value="{{ old('qty', $product->qty) }}"
                                                placeholder="Qty">
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
                                        <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Block
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product category</h2>
                                <div class="mb-3">
                                    <label for="category">Category</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ isset($product) && $product->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="sub_category">Sub Category</label>
                                    <select name="sub_category_id" id="sub_category_id" class="form-control">
                                        <option value="">Select Sub Category</option>
                                        <!-- Sub-categories will be populated here -->
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
                                        <option value="0"
                                            {{ isset($product) && $product->is_featured == 0 ? 'selected' : '' }}>No
                                        </option>
                                        <option value="1"
                                            {{ isset($product) && $product->is_featured == 1 ? 'selected' : '' }}>Yes
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Related product</h2>
                                <div class="mb-3">
                                    <select multiple name="related_products[]" id="related_products"
                                        class="form-control">
                                        @if (!empty($relatedProducts))
                                            @foreach ($relatedProducts as $item)
                                                <option value="{{ $item->id }}" selected>{{ $item->title }}</option>
                                            @endforeach
                                        @endif

                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
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

            // Auto-generate slug from the title field
            $('#title').on('keyup', function() {
                var name = $(this).val();
                var slug = name.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '') // Remove invalid characters
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/-+/g, '-'); // Replace multiple hyphens with a single hyphen
                $('#slug').val(slug); // Set the slug field

                // Clear slug field error when title field changes
                $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
            });

            // Clear validation error when typing in the title field
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
                    url: "{{ route('products.update', $product->id) }}", // Update with the correct route and product ID
                    type: "PUT", // Use PUT for update
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
                    error: function(jqXHR, exception, textStatus, errorThrown) {

                        console.log('Status:', jqXHR.status);
                        console.log('Status Text:', textStatus);
                        console.log('Error Thrown:', errorThrown);
                        console.log('Response Text:', jqXHR.responseText);


                        var response = jqXHR.responseJSON;
                        if (response && response.errors) {
                            var errors = response.errors;

                            // Function to show validation errors
                            function showErrors(field) {
                                if (errors[field]) {
                                    $("#" + field).addClass('is-invalid')
                                        .siblings('p').addClass('invalid-feedback')
                                        .html(errors[
                                            field][0]);
                                } else {
                                    $("#" + field).removeClass('is-invalid').siblings(
                                            'p')
                                        .removeClass('invalid-feedback').html('');
                                }
                            }

                            // Fields to check for validation errors
                            ['title', 'slug', 'description', 'price', 'compare_price',
                                'sku',
                                'barcode', 'qty'
                            ].forEach(showErrors);

                            // SweetAlert error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'An error occurred while updating the product. Please try again.',
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

            function loadSubCategories(categoryId, selectedSubCategory = null) {
                $.ajax({
                    type: "get",
                    url: "{{ route('products.sub_category') }}",
                    data: {
                        category_id: categoryId
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#sub_category_id").find("option").not(":first").remove();
                        $.each(response["sub_categories"], function(key, value) {
                            $("#sub_category_id").append(
                                `<option value="${value.id}" ${selectedSubCategory == value.id ? 'selected' : ''}>${value.name}</option>`
                            );
                        });
                    }
                });
            }

            // Load sub-categories when the category field changes
            $("#category_id").change(function() {
                var categoryId = $(this).val();
                loadSubCategories(categoryId);
            });

            // If editing, pre-select the category and sub-category
            @if (isset($product))
                var selectedCategory = "{{ $product->category_id }}";
                var selectedSubCategory = "{{ $product->sub_category_id }}";

                if (selectedCategory) {
                    loadSubCategories(selectedCategory, selectedSubCategory);
                }
            @endif

            // Initialize Dropzone
            const dropzone = new Dropzone("#image", {
                url: "{{ route('temp-images.update') }}", // Ensure this route stores temporary images
                maxFiles: 10,
                paramName: 'image',
                params: {
                    'product_id': {{ $product->id }}
                },
                addRemoveLinks: true,
                acceptedFiles: "image/jpeg,image/png,image/gif",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for Laravel
                },
                success: function(file, response) {
                    var html = `<div class="col-md-3 m-3" id="image-row-${response.image_id}">
                    <div class="card">
                        <input type="hidden" name="image_array[]" value="${response.image_id}">
                        <div class="ratio ratio-4x3">
                            <img src="${response.image_path}" class="card-img-top img-fluid"
                                 style="width: 300px; height: 275px; object-fit: cover;" alt="...">
                        </div>
                        <div class="card-body">
                            <a href="javascript:void(0)" onclick="deleteImage(${response.image_id})" class="btn btn-danger w-100">Delete</a>
                        </div>
                    </div>
                </div>`;
                    $("#product-gallery").append(html);
                },
                complete: function(file) {
                    this.removeFile(file); // Remove the file from Dropzone after processing
                }
            });


            // Define the deleteImage function
            window.deleteImage = function(imageId) {
                // Remove image from the UI
                $("#image-row-" + imageId).remove();

                // Find or create the hidden input field for remove_images[]
                let removeImagesInput = $("input[name='remove_images[]']");
                if (removeImagesInput.length === 0) {
                    // If the input doesn't exist, create it and append to the form
                    $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "remove_images[]")
                        .attr("id", "remove_image_" + imageId)
                        .val(imageId)
                        .appendTo("form"); // Assuming this is the form you submit
                } else {
                    // If it exists, create a new hidden input for each removed image
                    $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "remove_images[]")
                        .attr("id", "remove_image_" + imageId)
                        .val(imageId)
                        .appendTo("form");
                }

                // Optionally, send an AJAX request to remove the image from the server
                $.ajax({
                    url: "{{ route('temp-images.delete') }}", // Ensure this route deletes the image
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        image_id: imageId
                    },
                    success: function(response) {
                        console.log('Image deleted successfully.');
                    },
                    error: function(xhr) {
                        console.log('Error deleting image:', xhr.responseText);
                    }
                });
            }

        });
    </script>
@endsection
