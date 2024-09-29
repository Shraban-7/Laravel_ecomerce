<?php

use App\Mail\OrderMail;
use App\Models\Brand;
use App\Models\category;
use App\Models\Order;
use App\Models\Page;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Mail;

function getCategories()
{

    return category::orderBy('name', 'ASC')->with('sub_category')->where('is_home', 1)->get();
}

function category()
{
    return category::orderBy('name', 'ASC')->with('sub_category')->where('status', 1)->get();
}

function brand()
{
    return Brand::orderBy('name', 'ASC')->where('status', 1)->get();
}

function getProductImage($productId)
{
    return ProductImage::where('product_id', $productId)->first();
}

function orderEmail($orderId, $recipientType)
{
    // Fetch the order with order items
    $order = Order::with('orderItems.product')->findOrFail($orderId);

    // Determine the recipient email based on the recipient type
    if ($recipientType === 'customer' && $order->user) {
        $recipientEmail = $order->user->email; // Get the user's email
    } elseif ($recipientType === 'admin') {
        // Using the admin email from the .env file
        $recipientEmail = env('ADMIN_EMAIL', 'admin@example.com'); // Set a default admin email if not found
    } else {
        // Handle the case where there is no user associated with the order
        return response()->json(['error' => 'Invalid recipient type or no user associated with this order.'], 404);
    }

    // Send the email
    Mail::to($recipientEmail)->send(new OrderMail($order, $recipientType));

    // Optionally return a response
    return response()->json(['message' => 'Email sent successfully!', 'order' => $order]);
}

function getStaticPages(){
    return Page::orderBy('name','asc')->get();
}
