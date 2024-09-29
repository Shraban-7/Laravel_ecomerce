<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\TempImagesController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\admin\DiscountCouponController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\Admin\ProductSubCategoryController;
use App\Http\Controllers\admin\SettingController;

Route::get('/', [FrontendController::class, 'index'])->name('frontend.home');
Route::get('/shop/{categorySlug?}/{subcategorySlug?}', [ShopController::class, 'index'])->name('frontend.shop');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name('frontend.product');
Route::get('/get-related-products', [ProductController::class, 'getRelatedProducts'])->name('related_product');
Route::get('/cart', [CartController::class, 'cart'])->name('frontend.cart');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('frontend.add_to_cart');
Route::post('/update-cart', [CartController::class, 'updateCart'])->name('frontend.update_cart');
Route::post('/delete-cart', [CartController::class, 'deleteItem'])->name('frontend.delete_cart');
Route::get('/checkout', [CartController::class, 'checkout'])->name('frontend.checkout');
Route::post('/process-checkout', [CartController::class, 'processCheckout'])->name('frontend.process.checkout');
Route::post('/get-order-summary', [CartController::class, 'getOrderSummary'])->name('frontend.getOrderSummary');
Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('frontend.applyDiscount');
Route::post('/remove-discount', [CartController::class, 'removeCoupon'])->name('frontend.removeDiscount');
Route::post('/add-to-wishlist', [FrontendController::class, 'addToWishlist'])->name('frontend.add_to_wishlist');
Route::get('/page/{slug}', [FrontendController::class, 'page'])->name('frontend.page');
Route::post('/contact', [FrontendController::class, 'sendContactEmail'])->name('contact.send');
Route::get('/forget-password', [AuthController::class, 'forgotPassword'])->name('user.forget_password');
Route::post('/forget-password', [AuthController::class, 'processForgotPassword'])->name('user.process.forget_password');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('user.reset_password');
Route::post('/process-reset-password', [AuthController::class, 'processResetPassword'])->name('user.process.reset_password');
Route::post('/submit-review', [ShopController::class, 'submitReview'])->name('submit.review');

// Route::get('/test',function(){
//     orderEmail(8);
// });

//Auth
Route::group(['prefix' => 'account'], function () {
    Route::group(['middleware' => 'guest'], function () {
        Route::get('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/register-save', [AuthController::class, 'registerStore'])->name('register.save');
        Route::get('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/login-save', [AuthController::class, 'authenticate'])->name('login.save');
    });

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::get('/change-password', [AuthController::class, 'changePassword'])->name('user.changePassword');
        Route::post('/change-password/save', [AuthController::class, 'changePasswordSave'])->name('user.changePasswordSave');
        Route::post('/update-address', [AuthController::class, 'updateAddress'])->name('update.address');
        Route::get('/my-orders', [AuthController::class, 'myOrders'])->name('account.myOrders');
        Route::get('/my-orders-details/{id}', [AuthController::class, 'orderDetails'])->name('account.orderDetails');
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/thank-you/{orderId}', [CartController::class, 'thank_you'])->name('frontend.thank_you');
        Route::get('/wishlist', [FrontendController::class, 'wishlist'])->name('frontend.wishlist');
        Route::post('/remove-from-wishlist', [FrontendController::class, 'removeFromWishlist'])->name('frontend.remove_from_wishlist');

    });
});

Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');

    });
    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [DashboardController::class, 'logout'])->name('admin.logout');

        // category

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.list');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories/save', [CategoryController::class, 'store'])->name('categories.save');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // category

        Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('sub_categories.list');
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('sub_categories.create');
        Route::post('/sub-categories/save', [SubCategoryController::class, 'store'])->name('sub_categories.save');
        Route::get('/sub-categories/{subcategory}/edit', [SubCategoryController::class, 'edit'])->name('sub_categories.edit');
        Route::put('/sub-categories/{subcategory}', [SubCategoryController::class, 'update'])->name('sub_categories.update');
        Route::delete('/sub-categories/{id}', [SubCategoryController::class, 'destroy'])->name('sub_categories.destroy');

        // Brand

        Route::get('/brands', [BrandController::class, 'index'])->name('brands.list');
        Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('/brands/save', [BrandController::class, 'store'])->name('brands.save');
        Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');

        // Product

        Route::get('/products', [ProductController::class, 'index'])->name('products.list');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products/save', [ProductController::class, 'store'])->name('products.save');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/products/rating', [ProductController::class, 'product_rating'])->name('products.rating');
        Route::post('/product-ratings/{id}/status', [ProductController::class, 'updateProductStatus'])->name('product-ratings.update-status');


        //temporary image create
        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');
        Route::post('/update-temp-image', [TempImagesController::class, 'update'])->name('temp-images.update');
        Route::delete('/temp-images/delete', [TempImagesController::class, 'delete'])->name('temp-images.delete');

        //shipping

        Route::get('/shipping/create', [ShippingController::class, 'create'])->name('shipping.create');
        Route::post('/shipping/save', [ShippingController::class, 'store'])->name('shipping.store');
        Route::get('/shipping/edit/{id}', [ShippingController::class, 'edit'])->name('shipping.edit'); // Route to display the edit form
        Route::post('/shipping/update/{id}', [ShippingController::class, 'update'])->name('shipping.update'); // Route to update shipping details
        Route::delete('/shipping/delete/{id}', [ShippingController::class, 'destroy'])->name('shipping.destroy');

        //discount coupon

        Route::get('discount-coupons', [DiscountCouponController::class, 'index'])->name('discount_coupons.index');
        Route::get('discount-coupons/create', [DiscountCouponController::class, 'create'])->name('discount_coupons.create');
        Route::post('discount-coupons', [DiscountCouponController::class, 'store'])->name('discount_coupons.store');
        Route::get('discount-coupons/{id}/edit', [DiscountCouponController::class, 'edit'])->name('discount_coupons.edit');
        Route::put('discount-coupons/{id}', [DiscountCouponController::class, 'update'])->name('discount_coupons.update');
        Route::delete('discount-coupons/{id}', [DiscountCouponController::class, 'destroy'])->name('discount_coupons.destroy');

        //Order

        Route::get('orders', [OrderController::class, 'index'])->name('admin.orders');
        Route::get('orders-detail/{id}', [OrderController::class, 'detail'])->name('admin.orders.detail');
        Route::put('orders/{order}/status', [OrderController::class, 'update'])->name('admin.orders.update');

        //Invoice admin
        Route::post('orders/{orderId}/send-invoice', [OrderController::class, 'sendInvoiceEmail'])->name('admin.orders.sendInvoice');

        // sub category scrap

        Route::get('/product-sub-category', [ProductSubCategoryController::class, 'index'])->name('products.sub_category');

        //users

        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index'); // List users
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create'); // List users
        Route::post('users/store', [UserController::class, 'store'])->name('admin.users.store'); // Store a new user
        Route::get('users/edit/{id}', [UserController::class, 'edit'])->name('admin.users.edit'); // Edit user
        Route::put('users/update/{id}', [UserController::class, 'update'])->name('admin.users.update'); // Update user
        Route::delete('users/delete/{id}', [UserController::class, 'destroy'])->name('admin.users.delete');

        //admin static pages

        Route::get('/pages', [PageController::class, 'index'])->name('pages.index'); // List users
        Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create'); // List users
        Route::post('pages/store', [PageController::class, 'store'])->name('pages.store'); // Store a new user
        Route::get('pages/edit/{id}', [PageController::class, 'edit'])->name('pages.edit'); // Edit user
        Route::put('pages/update/{id}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('pages/delete/{id}', [PageController::class, 'destroy'])->name('pages.delete');

        //Admin password reset

        Route::get('/change-password', [SettingController::class, 'changePassword'])->name('admin.changePassword');
        Route::post('/change-password/save', [SettingController::class, 'changePasswordSave'])->name('admin.changePasswordSave');
    });
});
