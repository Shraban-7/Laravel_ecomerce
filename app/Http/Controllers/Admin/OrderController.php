<?php

namespace App\Http\Controllers\admin;

use App\Models\Order;
use App\Mail\OrderMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Get the keyword from the request
        $keyword = $request->get('keyword');

        // Fetch all orders, optionally filtering by keyword
        $orders = Order::with('user', 'orderItems')
            ->when($keyword, function ($query, $keyword) {
                // Filter by user name, email, or order ID
                return $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                })
                    ->orWhere('id', 'like', "%{$keyword}%"); // Assuming order ID is searchable
            })
            ->orderBy('created_at', 'desc') // Order by creation date
            ->paginate(10); // Adjust the number of items per page as needed

        // Pass the orders data to the admin orders view
        return view('admin.orders.list', compact('orders', 'keyword'));
    }

    public function detail($id)
    {
        // Fetch the order along with user, order items, and shipping address
        $order = Order::with(['user', 'orderItems.product', 'shippingAddress.country'])->findOrFail($id);

        return view('admin.orders.detail', compact('order'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:pending,shipping,delivered,cancelled', // Ensure status is valid
            'shipped_date' => 'nullable|date|after:now', // Validate that shipped_date is in the future
        ]);

        // Fetch the order
        $order = Order::findOrFail($id);

        // Update the status
        $order->status = $request->status;

        // If status is shipping or delivered, update the shipped_date if provided
        // Handle shipped date based on status
        if ($request->status === 'shipping' || $request->status === 'delivered') {
            if ($request->filled('shipped_date')) {
                $order->shipped_date = $request->shipped_date; // Use the provided shipped_date
            } else {
                // Set to now if delivered and shipped_date is not provided
                if ($request->status === 'delivered') {
                    $order->shipped_date = now(); // Set to current date if delivered
                }
            }
        } else if ($request->status === 'pending' || $request->status === 'canceled') {
            // Clear the shipped_date if status is pending or canceled
            $order->shipped_date = null; // This will clear any previous value
        }

        // Save the order
        $order->save();

        // Return a response
        return response()->json(['status' => true, 'message' => 'Order status updated successfully!']);
    }

    public function sendInvoiceEmail(Request $request, $orderId)
    {
        $request->validate([
            'recipient' => 'required|in:customer,admin',
        ]);

        $recipientType = $request->input('recipient');

        // Call the helper function to send the email
        $response = $this->orderEmail($orderId, $recipientType);

        return $response; // Return the response from the email function
    }

    private function orderEmail($orderId, $recipientType)
    {
        // Fetch the order with order items
        $order = Order::with('orderItems.product')->findOrFail($orderId);

        // Determine the recipient email based on the recipient type
        if ($recipientType === 'customer' && $order->user) {
            $recipientEmail = $order->user->email; // Get the user's email
        } elseif ($recipientType === 'admin') {
            // Using the admin email from the .env file
            $recipientEmail = env('ADMIN_EMAIL', 'admin@admin.com'); // Set the admin email
        } else {
            // Handle the case where there is no user associated with the order
            return response()->json(['error' => 'Invalid recipient type or no user associated with this order.'], 404);
        }

        // Send the email
        Mail::to($recipientEmail)->send(new OrderMail($order, $recipientType));

        // Optionally return a response
        return response()->json(['message' => 'Email sent successfully!', 'order' => $order]);
    }

}
