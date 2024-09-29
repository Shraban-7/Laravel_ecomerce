@extends('frontend.layouts.app')


@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('frontend.home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Shop</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-6 pt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 sidebar">
                    <div class="sub-title">
                        <h2>Categories</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="accordion accordion-flush" id="accordionExample">
                                @foreach (category() as $key => $cat)
                                    <div class="accordion-item">
                                        @if ($cat->sub_category->isNotEmpty())
                                            <h2 class="accordion-header" id="headingOne-{{ $key }}">
                                                <button
                                                    class="accordion-button collapsed {{ $categorySelected == $cat->id ? 'text-primary' : '' }}"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseOne-{{ $key }}" aria-expanded="false"
                                                    aria-controls="collapseOne-{{ $key }}">
                                                    {{ $cat->name }}
                                                </button>
                                            </h2>
                                        @else
                                            <a href="{{ route('frontend.shop', $cat->slug) }}"
                                                class="nav-item nav-link {{ $categorySelected == $cat->id ? 'text-primary' : '' }}">{{ $cat->name }}</a>
                                        @endif
                                        @if ($cat->sub_category->isNotEmpty())
                                            <div id="collapseOne-{{ $key }}"
                                                class="accordion-collapse collapse {{ $categorySelected == $cat->id ? 'show' : '' }}"
                                                aria-labelledby="headingOne-{{ $key }}"
                                                data-bs-parent="#accordionExample" style="">
                                                <div class="accordion-body">
                                                    <div class="navbar-nav">

                                                        @foreach ($cat->sub_category as $sub_cat)
                                                            <a href="{{ route('frontend.shop', [$cat->slug, $sub_cat->slug]) }}"
                                                                class="nav-item nav-link {{ $subcategorySelected == $sub_cat->id ? 'text-primary' : '' }}">{{ $sub_cat->name }}</a>
                                                        @endforeach

                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="sub-title mt-5">
                        <h2>Brand</h2>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            @foreach (brand() as $brand)
                                <div class="form-check mb-2">
                                    <input {{ in_array($brand->id, $brandArray) ? 'checked' : '' }}
                                        class="form-check-input brand-label" type="checkbox" name="brand[]"
                                        value="{{ $brand->id }}" id="brand-{{ $brand->id }}">
                                    <label class="form-check-label" for="brand-{{ $brand->id }}">
                                        {{ $brand->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="sub-title mt-5">
                        <h2>Price</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <input type="text" class="js-range-slider" name="my_range" value=""
                                data-type="double" />
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row pb-3">
                        <div class="col-12 pb-1">
                            <div class="d-flex align-items-center justify-content-end mb-4">
                                <div class="ml-2">
                                    <select name="sort" id="sort" class="form-control">
                                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest
                                        </option>
                                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                            Price Low</option>
                                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                            Price High</option>
                                    </select>

                                </div>
                            </div>
                        </div>


                        @foreach ($products as $prod)
                            @php
                                // Fetch the first image from the product_images relationship
                                $prod_img = $prod->product_images->first();
                            @endphp
                            <div class="col-md-4">
                                <div class="card product-card">
                                    <div class="product-image position-relative">
                                        <a href="{{ route('frontend.product', $prod->slug) }}" class="product-img">
                                            <img class="card-img-top"
                                                src="{{ asset('uploads/product/small/' . ($prod_img ? $prod_img->image : 'default.jpg')) }}"
                                                alt="{{ $prod->title }}">
                                        </a>
                                        <a class="whishlist" href="#"><i class="far fa-heart"></i></a>

                                        <div class="product-action">
                                            @if ($prod->track_qty == 1)
                                                @if ($prod->qty > 0)
                                                    <a class="btn btn-dark" href="javascript:void(0);"
                                                        onclick="addToCart({{ $prod->id }});">
                                                        <i class="fa fa-shopping-cart"></i> Add To Cart
                                                    </a>
                                                @else
                                                    <a class="btn btn-dark" href="javascript:void(0);">
                                                        <i class="fa fa-shopping-cart"></i> Out of stock
                                                    </a>
                                                @endif
                                            @else
                                                <a class="btn btn-dark" href="javascript:void(0);"
                                                    onclick="addToCart({{ $prod->id }});">
                                                    <i class="fa fa-shopping-cart"></i> Add To Cart
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body text-center mt-3">
                                        <a class="h6 link"
                                            href="{{ route('frontend.product', $prod->id) }}">{{ $prod->title }}</a>
                                        <div class="price mt-2">
                                            <span class="h5"><strong>${{ $prod->price }}</strong></span>
                                            <span class="h6 text-underline"><del>${{ $prod->compare_price }}</del></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach



                        <div class="col-md-12 pt-5">
                            <nav aria-label="Page navigation example">
                                {{ $products->withQueryString()->links() }}
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        // Initialize the price range slider
        $(".js-range-slider").ionRangeSlider({
            type: "double",
            min: 0,
            max: 1000,
            from: {{ $priceMin }},
            to: {{ $priceMax }},
            grid: true,
            skin: 'round',
            prefix: "$",
            max_postfix: "+",
            onFinish: function() {
                apply_filters(); // Apply filters when the range slider finishes
            }
        });

        var slider = $(".js-range-slider").data('ionRangeSlider');

        // Detect change in brand selection
        $('.brand-label').change(function() {
            apply_filters();
        });

        // Detect change in sorting option
        $('#sort').change(function() {
            apply_filters();
        });

        // Function to apply filters and redirect to the updated URL
        function apply_filters() {
            var selectedBrands = [];
            var sort = $('#sort').val();
            var keyword = $('#search').val()

            // Collect all selected brand IDs
            $('.brand-label:checked').each(function() {
                selectedBrands.push($(this).val());
            });

            // Get the base URL (current page URL without any query parameters)
            var baseUrl = '{{ url()->current() }}';
            var params = new URLSearchParams();

            // Set price range in the query params (only if necessary)
            if (slider.result.from !== 0 || slider.result.to !== 1000) {
                params.set('price_min', slider.result.from);
                params.set('price_max', slider.result.to);
            }

            // Set selected brands in the query params
            if (selectedBrands.length > 0) {
                params.set('brand', selectedBrands.join(','));
            }


            if (keyword.length > 0) {
                params.set('search', keyword);
            }

            // Set sorting option in the query params
            if (sort) {
                params.set('sort', sort);
            }

            // Construct the final URL without extra "&"
            var queryString = params.toString();
            var finalUrl = baseUrl + (queryString ? '?' + queryString : '');

            // Redirect to the updated URL with filters
            window.location.href = finalUrl;
        }
    </script>
@endsection
