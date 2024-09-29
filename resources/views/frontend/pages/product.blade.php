@extends('frontend.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('frontend.home') }}">Home</a></li>
                    <li class="breadcrumb-item">
                        <a class="white-text"
                            href="{{ route('frontend.shop', ['categorySlug' => null, 'subcategorySlug' => null]) }}">Shop</a>
                    </li>

                    <li class="breadcrumb-item">{{ $product->title }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-7 pt-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col-md-5">
                    <div id="product-carousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner bg-light">
                            @if ($product->product_images)
                                @foreach ($product->product_images as $key => $product_img)
                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                        <img class="w-100 h-100"
                                            src="{{ asset('uploads/product/large/' . $product_img->image) }}"
                                            alt="Image">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <a class="carousel-control-prev" href="#product-carousel" data-bs-slide="prev">
                            <i class="fa fa-2x fa-angle-left text-dark"></i>
                        </a>
                        <a class="carousel-control-next" href="#product-carousel" data-bs-slide="next">
                            <i class="fa fa-2x fa-angle-right text-dark"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="bg-light right">
                        <h1>{{ $product->title }}</h1>
                        <div class="d-flex mb-3">
                            <div class="text-primary mr-2">
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star-half-alt"></small>
                                <small class="far fa-star"></small>
                            </div>
                            <small class="pt-1">(99 Reviews)</small>
                        </div>
                        <h2 class="price text-secondary"><del>${{ $product->compare_price }}</del></h2>
                        <h2 class="price ">${{ $product->price }}</h2>

                        {!! $product->short_description !!}
                        @if ($product->track_qty == 1)
                            @if ($product->qty > 0)
                                <a class="btn btn-dark" href="javascript:void(0);"
                                    onclick="addToCart({{ $product->id }});">
                                    <i class="fa fa-shopping-cart"></i> Add To Cart
                                </a>
                            @else
                                <a class="btn btn-dark" href="javascript:void(0);">
                                    <i class="fa fa-shopping-cart"></i> Out of stock
                                </a>
                            @endif
                        @else
                            <a class="btn btn-dark" href="javascript:void(0);" onclick="addToCart({{ $product->id }});">
                                <i class="fa fa-shopping-cart"></i> Add To Cart
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-md-12 mt-5">
                    <div class="bg-light">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                    data-bs-target="#description" type="button" role="tab" aria-controls="description"
                                    aria-selected="true">Description</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping"
                                    type="button" role="tab" aria-controls="shipping" aria-selected="false">Shipping &
                                    Returns</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews"
                                    type="button" role="tab" aria-controls="reviews"
                                    aria-selected="false">Reviews</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel"
                                aria-labelledby="description-tab">

                                {!! $product->description !!}

                            </div>
                            <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                                {!! $product->shipping_returns !!}
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <div class="col-md-8">
                                    <div class="row">
                                        <form action="" id="reviewForm" name="reviewForm">
                                            <h3 class="h4 pb-3">Write a Review</h3>
                                            <input type="hidden" name="product_id" value="{{ $product->id }}"
                                                id="product_id">
                                            <div class="form-group col-md-6 mb-3">
                                                <label for="username">Name</label>
                                                <input type="text" class="form-control" name="username"
                                                    id="username" placeholder="Name">
                                            </div>
                                            <div class="form-group col-md-6 mb-3">
                                                <label for="email">Email</label>
                                                <input type="text" class="form-control" name="email" id="email"
                                                    placeholder="Email">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="rating">Rating</label>
                                                <br>
                                                <div class="rating" style="width: 10rem">
                                                    <input id="rating-5" type="radio" name="rating"
                                                        value="5" /><label for="rating-5"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-4" type="radio" name="rating"
                                                        value="4" /><label for="rating-4"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-3" type="radio" name="rating"
                                                        value="3" /><label for="rating-3"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-2" type="radio" name="rating"
                                                        value="2" /><label for="rating-2"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-1" type="radio" name="rating"
                                                        value="1" /><label for="rating-1"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="">How was your overall experience?</label>
                                                <textarea name="comment" id="review" class="form-control" cols="30" rows="10"
                                                    placeholder="How was your overall experience?"></textarea>
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-dark">Submit</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                                <div class="col-md-12 mt-5">
                                    <div class="overall-rating mb-3">
                                        <div class="d-flex">
                                            <!-- Display the Average Rating (rounded to 1 decimal) -->
                                            <h1 class="h3 pe-3">{{ number_format($averageRating, 1) }}</h1>

                                            <!-- Star Rating -->
                                            <div class="star-rating mt-2" title="{{ $averageRating * 20 }}%">
                                                <div class="back-stars">
                                                    <!-- Display 5 empty stars -->
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>

                                                    <!-- Front Stars (filled according to average rating) -->
                                                    <div class="front-stars" style="width: {{ ($averageRating / 5) * 100 }}%">
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Display the Review Count -->
                                            <div class="pt-2 ps-2">({{ $reviewCount }} Reviews)</div>
                                        </div>


                                    </div>
                                    @forelse ($latestReviews as $review)
                                    <div class="rating-group mb-4">
                                        <!-- Display Username -->
                                        <span><strong>{{ $review->username }}</strong></span>

                                        <!-- Star Rating -->
                                        <div class="star-rating mt-2" title="{{ $review->rating * 20 }}%">
                                            <div class="back-stars">
                                                <!-- Display 5 empty stars -->
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>

                                                <!-- Front Stars (filled according to rating) -->
                                                <div class="front-stars" style="width: {{ ($review->rating / 5) * 100 }}%">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Review Content -->
                                        <div class="my-3">
                                            <p>{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p>No reviews available.</p>
                                @endforelse




                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (!empty($relatedProducts))
        <section class="pt-5 section-8">
            <div class="container">
                <div class="section-title">
                    <h2>Related Products</h2>
                </div>
                <div class="col-md-12">
                    <div id="related-products" class="carousel">
                        @foreach ($relatedProducts as $item)
                            @php
                                $product_img = $item->product_images->first();
                            @endphp
                            <div class="card product-card">
                                <div class="product-image position-relative">
                                    <a href="{{ route('frontend.product', $item->slug) }}" class="product-img">
                                        @if (!empty($product_img->image))
                                            <img class="card-img-top"
                                                src="{{ asset('uploads/product/small/' . $product_img->image) }}"
                                                alt="{{ $item->title }}">
                                        @else
                                            <img class="card-img-top"
                                                src="{{ asset('admin_assets/img/default-product.png') }}"
                                                alt="{{ $item->title }}">
                                        @endif
                                    </a>
                                    <a class="whishlist" href="222"><i class="far fa-heart"></i></a>

                                    <div class="product-action">
                                        @if ($product->track_qty == 1)
                                            @if ($product->qty > 0)
                                                <a class="btn btn-dark" href="javascript:void(0);"
                                                    onclick="addToCart({{ $product->id }});">
                                                    <i class="fa fa-shopping-cart"></i> Add To Cart
                                                </a>
                                            @else
                                                <a class="btn btn-dark" href="javascript:void(0);">
                                                    <i class="fa fa-shopping-cart"></i> Out of stock
                                                </a>
                                            @endif
                                        @else
                                            <a class="btn btn-dark" href="javascript:void(0);"
                                                onclick="addToCart({{ $product->id }});">
                                                <i class="fa fa-shopping-cart"></i> Add To Cart
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body text-center mt-3">
                                    <a class="h6 link" href="">{{ $item->title }}</a>
                                    <div class="price mt-2">
                                        <span class="h5"><strong>${{ $item->price }}</strong></span>
                                        <span class="h6 text-underline"><del>${{ $item->compare_price }}</del></span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection


@section('js')
    <script>
        $(document).ready(function() {
            $('#reviewForm').submit(function(e) {
                e.preventDefault();

                let formData = {
                    product_id: $('#product_id').val(),
                    username: $('#username').val(),
                    email: $('#email').val(),
                    rating: $('input[name="rating"]:checked').val(),
                    comment: $('#review').val(),
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    type: 'POST',
                    url: "{{ route('submit.review') }}", // Define your route here
                    data: formData,
                    success: function(response) {
                        if (response.status == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Review Submitted!',
                                text: response.message,
                            });
                            $('#reviewForm')[0].reset(); // Reset the form after submission
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    },
                    error: function(jqXHR) {
                        let errors = jqXHR.responseJSON.errors;
                        if (errors.name) {
                            $('#name').addClass('is-invalid').after(
                                `<p class="text-danger">${errors.name[0]}</p>`);
                        }
                        if (errors.email) {
                            $('#email').addClass('is-invalid').after(
                                `<p class="text-danger">${errors.email[0]}</p>`);
                        }
                        if (errors.review) {
                            $('#review').addClass('is-invalid').after(
                                `<p class="text-danger">${errors.review[0]}</p>`);
                        }
                    }
                });
            });

            $('#name, #email, #review').on('input', function() {
                $(this).removeClass('is-invalid'); // Remove error classes on input
                $(this).siblings('.text-danger').remove(); // Remove error messages
            });
        });
    </script>
@endsection
