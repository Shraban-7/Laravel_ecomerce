<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountCouponController extends Controller
{
    // Display a listing of the coupons
    public function index()
    {
        $coupons = DiscountCoupon::all();
        return view('admin.discount_coupons.list', compact('coupons'));
    }

    // Show the form for creating a new coupon
    public function create()
    {
        return view('admin.discount_coupons.create');
    }

    // Store a newly created coupon in storage
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:discount_coupons,code',
            'name' => 'required|string|max:100',
            'discount_amount' => 'required|numeric|min:0',
            'start_at' => 'required|date|after_or_equal:today',
            'expires_at' => 'required|date|after:start_at',
        ]);

        // If validation fails, return validation errors as JSON
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create a new discount coupon
        $coupon = new DiscountCoupon();
        $coupon->code = $request->code;
        $coupon->name = $request->name;
        $coupon->discount_amount = $request->discount_amount;
        $coupon->description = $request->description; // Include description if needed
        $coupon->max_uses = $request->max_uses; // Include max_uses if needed
        $coupon->max_uses_user = $request->max_uses_user; // Include max_uses_user if needed
        $coupon->type = $request->type; // Include type if needed
        $coupon->min_amount = $request->min_amount; // Include min_amount if needed
        $coupon->start_at = $request->start_at; // Include start_at
        $coupon->expires_at = $request->expires_at; // Include expires_at
        $coupon->status = $request->status;
        $coupon->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Discount coupon created successfully!',
        ]);
    }

    // Show the form for editing the specified coupon
    public function edit($id)
    {
        $coupon = DiscountCoupon::findOrFail($id);
        $coupon->start_at = \Carbon\Carbon::parse($coupon->start_at)->format('Y-m-d\TH:i');
        $coupon->expires_at = \Carbon\Carbon::parse($coupon->expires_at)->format('Y-m-d\TH:i');
        return view('admin.discount_coupons.edit', [
            'coupon' => $coupon,
            'start_at' => $coupon->start_at,
            'expires_at' => $coupon->expires_at,
        ]);
    }

    // Update the specified coupon in storage
    public function update(Request $request, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:discount_coupons,code,' . $id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'max_uses' => 'nullable|integer|min:0',
            'max_uses_user' => 'nullable|integer|min:0',
            'type' => 'required|in:fixed,percentage',
            'discount_amount' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'start_at' => 'required|date|after_or_equal:today',
            'expires_at' => 'required|date|after:start_at',
            'status' => 'required|boolean',
        ]);

        // If validation fails, return validation errors as JSON
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find the discount coupon by ID
        $coupon = DiscountCoupon::findOrFail($id);

        // Update coupon data
        $coupon->code = $request->code;
        $coupon->name = $request->name;
        $coupon->description = $request->description;
        $coupon->max_uses = $request->max_uses;
        $coupon->max_uses_user = $request->max_uses_user;
        $coupon->type = $request->type;
        $coupon->discount_amount = $request->discount_amount;
        $coupon->min_amount = $request->min_amount;
        $coupon->start_at = \Carbon\Carbon::parse($request->start_at); // Handle date format
        $coupon->expires_at = \Carbon\Carbon::parse($request->expires_at); // Handle date format
        $coupon->status = $request->status;

        // Save the updated coupon
        $coupon->save();

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => 'Discount coupon updated successfully!',
        ]);
    }

    // Remove the specified coupon from storage
    public function destroy($id)
    {
        $coupon = DiscountCoupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon deleted successfully.',
        ]);
    }

}
