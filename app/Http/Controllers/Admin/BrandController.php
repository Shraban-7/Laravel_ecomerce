<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Brand::latest(); // Use the Brand model

        // Apply the search filter if a keyword is provided
        if (!empty($request->get('keyword'))) {
            $query->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        // Paginate the results
        $brands = $query->paginate(10);

        // Pass the brands to the view
        return view('admin.brand.list', compact('brands')); // Change to the brand view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.brand.create'); // Change to the brand creation view
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands', // Update to brands
            'status' => 'required|boolean',
        ]);

        try {
            // Auto-generate a unique slug from the name
            $slug = Str::slug($validatedData['name']); // Create a slug from the name
            $slugCount = Brand::where('slug', 'like', $slug . '%')->count(); // Use Brand model

            if ($slugCount > 0) {
                $slug = $slug . '-' . ($slugCount + 1); // Append number if slug is not unique
            }

            // Create a new brand
            $brand = new Brand();
            $brand->name = $validatedData['name'];
            $brand->slug = $slug;
            $brand->status = $validatedData['status'];
            $brand->save();

            // Return a success JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Brand saved successfully!',
                'data' => $brand,
            ]);

        } catch (\Exception $e) {
            // Catch and handle any errors
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving the brand',
                'error' => $e->getMessage(),
            ], 500); // Return a 500 Internal Server Error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($brandId, Request $request)
    {
        $brand = Brand::find($brandId); // Use the Brand model

        if (empty($brand)) {
            return redirect()->route('brands.list')->with('error', 'Brand does not exist.'); // Update route
        }
        return view('admin.brand.edit', compact('brand')); // Update to brand edit view
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($brandId, Request $request)
    {
        // Retrieve the brand using the given ID
        $brand = Brand::find($brandId); // Use Brand model

        if (empty($brand)) {
            return response()->json([
                'status' => false,
                'message' => 'Brand not found',
            ]);
        }

        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug,' . $brand->id, // Use Brand model
            'status' => 'required|boolean',
        ]);

        try {
            // Only generate a new slug if the name has changed
            if ($brand->name != $validatedData['name']) {
                $slug = Str::slug($validatedData['name']);
                $existingSlugCount = Brand::where('slug', 'like', $slug . '%')
                    ->where('id', '!=', $brand->id)
                    ->count();

                if ($existingSlugCount > 0) {
                    $slug = $slug . '-' . ($existingSlugCount + 1);
                }
                $brand->slug = $slug;
            }

            // Update the brand data
            $brand->name = $validatedData['name'];
            $brand->status = $validatedData['status'];
            $brand->save();

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Brand updated successfully!',
                'data' => $brand,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the brand',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($brandId)
    {
        // Find the brand by ID
        $brand = Brand::find($brandId); // Use the Brand model

        if (empty($brand)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found',
            ], 404);
        }

        try {
            // Delete the brand
            $brand->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Brand deleted successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the brand',
                'error' => $e->getMessage(), // Include the error message for debugging
            ], 500);
        }
    }
}
