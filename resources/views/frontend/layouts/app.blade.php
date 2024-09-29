<!DOCTYPE html>
<html lang="en_AU">

<head>
    <meta charset="UTF-8">
    <title>{{ !empty($title) ? 'Title-' . $title : 'Home' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=no">
    <meta name="HandheldFriendly" content="True">
    <meta name="pinterest" content="nopin">

    <!-- Open Graph Meta Tags -->
    <meta property="og:locale" content="en_AU">
    <meta property="og:type" content="website">
    <meta property="fb:admins" content="">
    <meta property="fb:app_id" content="">
    <meta property="og:site_name" content="">
    <meta property="og:title" content="">
    <meta property="og:description" content="">
    <meta property="og:url" content="">
    <meta property="og:image" content="">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="">
    <meta property="og:image:height" content="">
    <meta property="og:image:alt" content="">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:title" content="">
    <meta name="twitter:site" content="">
    <meta name="twitter:description" content="">
    <meta name="twitter:image" content="">
    <meta name="twitter:image:alt" content="">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('front-end/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('front-end/css/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('front-end/css/video-js.css') }}">
    <link rel="stylesheet" href="{{ asset('front-end/css/ion.rangeSlider.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front-end/css/style.css?v=' . rand(111, 999)) }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&family=Raleway:ital,wght@0,400;0,600;0,800;1,200&family=Roboto+Condensed:wght@400;700&family=Roboto:wght@300;400;700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="#">
</head>

<body data-instant-intensity="mousedown">

    <div class="bg-light top-header">
        <div class="container">
            <div class="row align-items-center py-3 d-none d-lg-flex justify-content-between">
                <div class="col-lg-4 logo">
                    <a href="{{ url('/') }}" class="text-decoration-none">
                        <span class="h1 text-uppercase text-primary bg-dark px-2">Online</span>
                        <span class="h1 text-uppercase text-dark bg-primary px-2 ml-n1">SHOP</span>
                    </a>
                </div>
                <div class="col-lg-6 col-6 text-left d-flex justify-content-end align-items-center">
                    @auth

                        <a href="{{ route('profile') }}" class="nav-link text-dark">My Account</a>
                    @endauth
                    @guest
                    <a href="{{ route('login') }}" class="nav-link text-dark">Login</a>
                    @endguest
                    <form action="{{ route('frontend.shop') }}">
                        <div class="input-group">
                            <input type="text" placeholder="Search For Products" value="{{ Request::get('search') }}" name="search" id="search" class="form-control"
                                aria-label="Search">
                            <button type="submit" class="input-group-text">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('frontend.partials.header')
    <main>
        @yield('content')
    </main>
    @include('frontend.partials.footer')

    <!-- Scripts -->
    <script src="{{ asset('front-end/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('front-end/js/bootstrap.bundle.5.1.3.min.js') }}"></script>
    <script src="{{ asset('front-end/js/instantpages.5.1.0.min.js') }}"></script>
    <script src="{{ asset('front-end/js/lazyload.17.6.0.min.js') }}"></script>
    <script src="{{ asset('front-end/js/slick.min.js') }}"></script>
    <script src="{{ asset('front-end/js/custom.js') }}"></script>
    <script src="{{ asset('front-end/js/ion.rangeSlider.min.js') }}"></script>
    <script src="{{ asset('admin_assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Include SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })


        window.onscroll = function() {
            myFunction();
        };

        //cart

        function addToCart(id) {
            $.ajax({
                type: "post",
                url: "{{ route('frontend.add_to_cart') }}",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == true) {
                        window.location.href = "{{ route('frontend.cart') }}"
                    } else {
                        alert(response.message)
                    }
                }
            });
        }

        var navbar = document.getElementById("navbar");
        var sticky = navbar.offsetTop;

        function myFunction() {
            if (window.pageYOffset >= sticky) {
                navbar.classList.add("sticky");
            } else {
                navbar.classList.remove("sticky");
            }
        }


        function addToWishlist(id) {
            $.ajax({
                type: "post",
                url: "{{ route('frontend.add_to_wishlist') }}",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == true) {
                        // window.location.href = "{{ route('frontend.cart') }}"
                        toastr.success(response.product_name + " " + response.message);
                    } else {
                        if (response.message === "Product not found") {
                            toastr.error("The product you are trying to add does not exist.");
                        } else {
                            window.location.href = "{{ route('login') }}";
                        }
                    }
                }
            });
        }
    </script>

    @yield('js')
</body>

</html>
