<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\ProductRating;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subcategorySlug = null)
    {
        $categorySelected = '';
        $subcategorySelected = '';
        $brandArray = [];

        $productsQuery = Product::with('product_images')->where('status', 1);

        // Filter by category if categorySlug is provided
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $productsQuery->where('category_id', $category->id);
                $categorySelected = $category->id;
            }
        }

        // Filter by subcategory if subcategorySlug is provided
        if (!empty($subcategorySlug)) {
            $subcategory = SubCategory::where('slug', $subcategorySlug)->first();
            if ($subcategory) {
                $productsQuery->where('sub_category_id', $subcategory->id);
                $subcategorySelected = $subcategory->id;
            }
        }

        // Filter by brand
        if (!empty($request->get('brand'))) {
            $brandArray = explode(',', $request->get('brand'));
            $productsQuery->whereIn('brand_id', $brandArray);
        }

        // Filter by price range
        if ($request->get('price_min') != '' && $request->get('price_max') != '') {
            if (intval($request->get('price_max')) >= 1000) {
                $productsQuery->whereBetween('price', [intval($request->get('price_min')), 100000000]);
            } else {
                $productsQuery->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
            }
        }

        $priceMin = intval($request->get('price_min'));
        $priceMax = (intval($request->get('price_max')) == 0) ? 1000 : intval($request->get('price_max')); // Corrected

        if (!empty($request->get('search'))) {
            $productsQuery->where('title', 'like', '%' . $request->get('search') . '%');
        }

        // Sort products based on the selected option
        if ($request->get('sort')) {
            if ($request->get('sort') == 'price_asc') {
                $productsQuery->orderBy('price', 'ASC');
            } elseif ($request->get('sort') == 'price_desc') {
                $productsQuery->orderBy('price', 'DESC');
            } elseif ($request->get('sort') == 'latest') {
                $productsQuery->orderBy('created_at', 'DESC'); // Ordering by creation date for latest
            }
        } else {
            // Default sorting when no sort parameter is provided
            $productsQuery->orderBy('id', 'DESC');
        }

        // Execute the query and get the products
        $products = $productsQuery->paginate(6);
        return view('frontend.pages.shop', compact('products', 'categorySelected', 'subcategorySelected', 'brandArray', 'priceMin', 'priceMax'));
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)->with(['product_images','reviews' => function ($query) {
        $query->where('status', 1);
    }])->first();

        $relatedProducts = [];

        if ($product->related_products != '') {
            $productArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)->where('status', 1)->get();
        }

        $latestReviews = ProductRating::where('status',1)->orderBy('created_at', 'desc')->take(2)->get();
        $averageRating = $product->reviews->avg('rating');
        $reviewCount = $product->reviews->count();
        return view('frontend.pages.product', compact('product', 'relatedProducts','latestReviews','averageRating', 'reviewCount'));
    }

    public function submitReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|email',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Save the review (assuming you have a Review model)
        ProductRating::create([
            'product_id'=>$request->product_id,
            'username' => $request->username,
            'email' => $request->email,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Review submitted successfully!',
        ]);
    }

}
