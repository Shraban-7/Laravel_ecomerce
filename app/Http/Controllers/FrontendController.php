<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class FrontendController extends Controller
{
    public function index()
    {
        $feature_products = Product::where('is_featured', 1)->take(8)->latest()->get();

        $latest_products = Product::orderBy('id')->latest()->get();

        return view('frontend.pages.home', compact('feature_products', 'latest_products'));
    }

    public function addToWishlist(Request $request)
    {
        if (Auth::check() == false) {
            session(['url.intended' => url()->previous()]);
            return response()->json([
                "status" => false,
            ]);
        }

        $product = Product::where('id', $request->id)->first();

        if (!$product) {
            return response()->json([
                "status" => false,
                "message" => "Product not found",
            ]);
        }

        $wishlist = Wishlist::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'product_id' => $product->id,
            ],
            [
                'user_id' => Auth::user()->id,
                'product_id' => $product->id,
            ]
        );

        return response()->json([
            "status" => true,
            "product_name" => $product->title,
            "message" => "add to wishlist successfully",
        ]);
    }

    public function wishlist()
    {
        if (Auth::check()) {
            // Retrieve wishlist items for the logged-in user
            $wishlistItems = Wishlist::where('user_id', Auth::id())
                ->with('product') // Assuming there's a 'product' relationship in the Wishlist model
                ->get();

            return view('frontend.auth.wishlist', compact('wishlistItems'));
        } else {
            // Redirect to login if the user is not authenticated
            return redirect()->route('login')->with('error', 'Please login to view your wishlist.');
        }
    }

    public function removeFromWishlist(Request $request)
    {
        // Find the wishlist item by its ID
        $wishlistItem = Wishlist::find($request->id);

        // Check if the item exists and belongs to the authenticated user
        if ($wishlistItem && $wishlistItem->user_id == Auth::id()) {
            // Delete the wishlist item
            $wishlistItem->delete();
            return response()->json([
                'status' => true,
                'message' => 'Item removed from wishlist successfully',
            ]);
        }

        // If the item was not found or the user is unauthorized
        return response()->json([
            'status' => false,
            'message' => 'Item not found or unauthorized',
        ]);
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->first();
        if ($page == null) {
            abort(404);
        }
        return view('frontend.pages.page', [
            "page" => $page,
        ]);
    }

    public function sendContactEmail(Request $request)
    {
        // Validate the form data
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        // Set up the email data
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'messageContent' => $request->message,
        ];

        // Send email to admin
        Mail::send('email.contact', $data, function ($message) use ($request) {
            $message->from($request->email, $request->name);
            $message->to('admin@admin.com', 'Admin Name') // Admin email address
                ->subject($request->subject);
        });

        // Return a response to the frontend (you can use JSON for AJAX or redirect for traditional form)
        return response()->json(['status' => 'success', 'message' => 'Message sent successfully!']);
    }

}
