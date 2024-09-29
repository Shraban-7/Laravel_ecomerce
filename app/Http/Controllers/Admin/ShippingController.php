<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create()
    {
        $countries = Country::all();

        $shippings = ShippingCharge::with('country')->get();

        return view('admin.shipping.create', compact('countries', 'shippings'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'country_id' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);

        // If validation fails, return validation errors as JSON
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create or update shipping data
        $shipping = new ShippingCharge();
        $shipping->country_id = $request->country_id;
        $shipping->amount = $request->amount;
        $shipping->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping details created successfully!',
        ]);
    }

    public function edit($id)
    {
        $shipping = ShippingCharge::findOrFail($id);
        $countries = Country::all();
        return view('admin.shipping.edit', compact('shipping', 'countries'));
    }

    public function update(Request $request, $id)
    {
        // Find the Shipping entry by ID
        $shipping = ShippingCharge::findOrFail($id);

        // Validate the request
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|unique:shipping_charges,country_id,' . $shipping->id,
            'amount' => 'required|numeric|min:0',
        ]);

        // If validation fails, return validation errors as JSON
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update the Shipping entry
        $shipping->update([
            'country_id' => $request->country_id,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping details updated successfully!',
        ]);
    }

    public function destroy($id)
    {
        // Find the Shipping entry by ID
        $shipping = ShippingCharge::findOrFail($id);

        // Delete the Shipping entry
        $shipping->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping details deleted successfully!',
        ]);
    }

}
