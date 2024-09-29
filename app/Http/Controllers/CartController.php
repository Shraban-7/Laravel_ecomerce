<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingCharge;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $product = Product::with('product_images')->find($request->id);

        if ($product == null) {
            return response()->json([
                "status" => false,
                "message" => 'Record not found',
            ]);
        }

        // Get the first product image if available
        $productImage = $product->product_images->isNotEmpty() ? $product->product_images->first() : '';

        // Check if cart has items
        if (Cart::count() > 0) {

            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach ($cartContent as $item) {
                if ($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }

            if ($productAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => $productImage]);
                return response()->json([
                    "status" => true,
                    "message" => 'Cart added successfully',
                ]);
            } else {
                return response()->json([
                    "status" => true,
                    "message" => 'Product added to the existing cart',
                ]);
            }

        } else {
            // Add product to cart
            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => $productImage]);

            return response()->json([
                "status" => true,
                "message" => 'Cart added successfully',
            ]);
        }
    }

    public function cart()
    {
        $cart_content = Cart::content();
        // return $cart_content;
        return view('frontend.pages.cart', compact('cart_content'));
    }

    public function updateCart(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);

        $product = Product::find($itemInfo->id);

        if ($product->track_qty == 1) {
            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);
                return response()->json([
                    "status" => true,
                    "message" => 'Cart updated successfully',
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => 'Required cart item not available in stock',
                ]);
            }

        } else {
            Cart::update($rowId, $qty);
            return response()->json([
                "status" => true,
                "message" => 'Cart updated successfully',
            ]);
        }
    }

    public function deleteItem(Request $request)
    {
        $rowId = $request->rowId;

        // Check if item exists in the cart
        $itemInfo = Cart::get($rowId);

        if (!$itemInfo) {
            return response()->json([
                "status" => false,
                "message" => 'Item not found in cart',
            ]);
        }

        // Remove item from the cart
        Cart::remove($rowId);

        return response()->json([
            "status" => true,
            "message" => 'Cart item removed successfully',
        ]);
    }

    public function checkout()
    {
        $discount = 0;

        // If the cart is empty, redirect to cart page
        if (Cart::count() == 0) {
            return redirect()->route('frontend.cart');
        }

        // If user is not logged in, redirect to login page
        if (!Auth::check()) {
            if (!session()->has('url.intended')) {
                session(['url.intended' => url()->current()]);
            }
            return redirect()->route('login');
        }

        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();
        session()->forget('url.intended'); // Clear intended URL session

        $countries = Country::all();
        $subTotal = Cart::subtotal(2, '.', '');

        // Apply discount if a coupon is applied
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percentage') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
        }

        // Initialize shipping details
        $totalShippingCharge = 0;
        $grandTotal = $subTotal - $discount; // Default grand total without shipping

        if ($customerAddress != null) {
            $customerCountry = $customerAddress->country_id;

            // Fetch shipping charge for the customer country
            $shippingCountry = ShippingCharge::where('country_id', $customerCountry)->first();

            // If shipping charge for country is not found, set a default shipping charge (e.g., 'rest-of-world')
            if ($shippingCountry == null) {
                $shippingCountry = ShippingCharge::where('country_id', 'rest-of-world')->first();
            }

            // If no shipping charge exists even for the default, handle accordingly
            if ($shippingCountry != null) {
                $totalQty = 0;

                // Calculate total quantity in cart
                foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
                }

                // Calculate total shipping charge based on quantity
                $totalShippingCharge = $totalQty * $shippingCountry->amount;
            }

            // Update grand total with shipping charge
            $grandTotal = ($subTotal - $discount) + $totalShippingCharge;
        }

        // Pass all necessary data to the view
        return view('frontend.pages.checkout', compact('countries', 'customerAddress', 'totalShippingCharge', 'grandTotal', 'discount'));
    }

    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'country_id' => 'required|exists:countries,id',
            'address' => 'required|string',
            'apartment' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip' => 'required|string|max:10',
        ]);

        // If validation fails, return JSON response with errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(), // Return validation errors
            ], 422);
        }

        $user = auth()->user();

        // Update or create customer address
        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country_id' => $request->country_id,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
            ]
        );

        if ($request->payment_method == 'cod') {

            //calculate shipping
            $discountCodeId = null;
            $promoCode = '';
            $discount = 0;
            $shipping = 0;
            $subTotal = Cart::subtotal(2, '.', '');
            $shippingInfo = ShippingCharge::where('country_id', $request->country_id)->first();
            $totalQty = 0;
            if (session()->has('code')) {
                $code = session()->get('code');
                if ($code->type == 'percentage') {
                    $discount = ($code->discount_amount / 100) * $subTotal;
                } else {
                    $discount = $code->discount_amount;
                }

                $discountCodeId = $code->id;
                $promoCode = $code->code;
            }
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }
            if ($shippingInfo != null) {

                $shipping = $totalQty * $shippingInfo->amount;
                $grandTotal = ($subTotal - $discount) + $shipping;
            } else {
                $shippingInfo = ShippingCharge::where('country_id', 'rest-of-world')->first();
                $shipping = $totalQty * $shippingInfo->amount;
                $grandTotal = ($subTotal - $discount) + $shipping;

            }
            // Calculate totals

            // Create a new order
            $order = new Order;
            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->phone = $request->phone;
            $order->country_id = $request->country_id;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->city = $request->city;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->notes = $request->notes;
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;
            $order->discount = $discount;
            $order->coupon_code_id = $discountCodeId;
            $order->coupon_code = $promoCode;
            $order->payment_status = 'not_paid';
            $order->status = 'pending';

            $order->save();

            // Save order items
            foreach (Cart::content() as $item) {
                $orderItem = new OrderItem;
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price * $item->qty;

                $orderItem->save();

                $productData = Product::find($item->id);
                if ($productData->track_qty == 1) {
                    $currentQty = $productData->qty;
                    $updatedQty = $currentQty - $item->qty;
                    $productData->qty = $updatedQty;
                    $productData->save();
                }

            }

            event(new OrderPlaced($order));

            Cart::destroy();
            session()->forget('code');
            // Return success response with orderId
            return response()->json([
                'status' => true,
                'orderId' => $order->id,
                'message' => 'Checkout processed successfully.',
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid payment method.']);
    }

    public function thank_you($orderId)
    {
        return view('frontend.pages.thank_you', compact('orderId'));
    }

    public function getOrderSummary(Request $request)
    {
        $subTotal = Cart::subtotal(2, '.', '');
        $discount = 0;
        $discountString = '';
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percentage') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }

            $discountString = '<div class="mt-4 p-3 d-flex justify-content-between align-items-center bg-light border rounded" id="discount-response">
                            <div>
                                <strong class="text-success">Applied Coupon: ' . session()->get('code')->code . '</strong>
                            </div>
                            <a class="btn btn-sm btn-danger text-white shadow-sm" id="remove-dicount">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>';
        }

        if ($request->country_id > 0) {
            $shippingInfo = ShippingCharge::where('country_id', $request->country_id)->first();

            if ($shippingInfo != null) {
                $totalQty = 0;
                foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
                }
                $shippingCharge = $totalQty * $shippingInfo->amount;
                $grandTotal = ($subTotal - $discount) + $shippingCharge;

                return response()->json([
                    'status' => true,
                    'discount' => $discount,
                    'discountString' => $discountString,
                    'grandTotal' => number_format($grandTotal, 2),
                    'shippingCharge' => number_format($shippingCharge),
                ]);
            } else {
                $shippingInfo = ShippingCharge::where('country_id', 'rest-of-world')->first();
                $totalQty = 0;
                foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
                }
                $shippingCharge = $totalQty * $shippingInfo->amount;
                $grandTotal = ($subTotal - $discount) + $shippingCharge;

                return response()->json([
                    'status' => true,
                    'discount' => $discount,
                    'discountString' => $discountString,
                    'grandTotal' => number_format($grandTotal, 2),
                    'shippingCharge' => number_format($shippingCharge),
                ]);
            }
        } else {
            return response()->json([
                'status' => true,
                'discount' => $discount,
                'discountString' => $discountString,
                'grandTotal' => number_format(($subTotal - $discount), 2),
                'shippingCharge' => 0,
            ]);
        }

    }

    public function applyDiscount(Request $request)
    {
        // Find the coupon by code
        $code = DiscountCoupon::where('code', $request->code)->first();

        // If the coupon does not exist
        if ($code == null) {
            return response()->json([
                "status" => false,
                "message" => "Invalid coupon code",
            ]);
        }

        $now = Carbon::now()->setTimezone('Asia/Dhaka');

        // Check if the coupon has a start date and if it hasn't started yet
        if ($code->start_at != null) {
            // Convert the start_at field to a Carbon instance, checking format
            try {
                $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->start_at);
            } catch (\Exception $e) {
                return response()->json([
                    "status" => false,
                    "message" => "Invalid start date format",
                ]);
            }

            // Compare both date and time (current datetime vs coupon start datetime)
            if ($now->lt($startDate)) {
                return response()->json([
                    "status" => false,
                    "message" => "Coupon code has not started yet",
                ]);
            }
        }

        // Check if the coupon has an expiration date and if it has expired
        if ($code->expires_at != null) {
            try {
                $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->expires_at);
            } catch (\Exception $e) {
                return response()->json([
                    "status" => false,
                    "message" => "Invalid expiration date format",
                ]);
            }

            if ($now->gt($endDate)) {
                return response()->json([
                    "status" => false,
                    "message" => "Coupon code has expired",
                ]);
            }
        }

        // Check coupon usage limit
        if ($code->max_uses > 0) {
            $couponUsed = Order::where('coupon_code_id', $code->id)->count(); // Correct the field name

            if ($couponUsed >= $code->max_uses) {
                return response()->json([
                    "status" => false,
                    "message" => "Coupon usage limit has been reached",
                ]);
            }
        }

        // Check usage limit per user

        if ($code->max_uses_user > 0) {
            $userCouponUsed = Order::where('coupon_code_id', $code->id)
                ->where('user_id', Auth::id()) // Assumes 'user_id' field exists in 'orders' table
                ->count();

            if ($userCouponUsed >= $code->max_uses_user) {
                return response()->json([
                    "status" => false,
                    "message" => "You have already used this coupon the maximum number of times",
                ]);
            }
        }

        $subTotal = Cart::subtotal(2, '.', '');

        // Min amount condition check

        if ($code->min_amount > 0) {
            if ($subTotal < $code->min_amount) {
                return response()->json([
                    "status" => false,
                    "message" => "Your minimum amount must be $" . $code->min_amount,
                ]);
            }
        }

        // Store the coupon in session
        session()->put('code', $code);

        // Return the updated order summary
        return $this->getOrderSummary($request);
    }

    public function removeCoupon(Request $request)
    {
        session()->forget('code');
        return $this->getOrderSummary($request);
    }

}
