<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SubCategory::with('category')->latest(); // Start with the query builder and eager load the category relationship

        // Apply the search filter if a keyword is provided
        if (!empty($request->get('keyword'))) {
            $keyword = $request->get('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('sub_categories.name', 'like', '%' . $keyword . '%') // Search in SubCategory name
                    ->orWhereHas('category', function ($q) use ($keyword) { // Search in related Category name
                        $q->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        // Paginate the results
        $sub_categories = $query->paginate(10);

        // Pass the categories to the view
        return view('admin.sub_category.list', compact('sub_categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = category::orderBy('name', 'asc')->get();
        return view('admin.sub_category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sub_categories',
            'category_id' => 'required',
            'is_home' => 'required|boolean',
            'status' => 'required|boolean',
        ]);

        try {
            // Auto-generate a unique slug from the name
            $slug = Str::slug($validatedData['name']); // Create a slug from the name
            $slugCount = Category::where('slug', 'like', $slug . '%')->count();

            if ($slugCount > 0) {
                $slug = $slug . '-' . ($slugCount + 1); // Append number if slug is not unique
            }

            // Create a new category
            $sub_category = new SubCategory;
            $sub_category->name = $validatedData['name'];
            $sub_category->slug = $slug;
            $sub_category->category_id = $validatedData['category_id'];
            $sub_category->is_home = $validatedData['is_home'];
            $sub_category->status = $validatedData['status'];
            $sub_category->save();

            // Return a success JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Category saved successfully!',
                'data' => $sub_category,
            ]);

        } catch (\Exception $e) {
            // Catch and handle any errors
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving the category',
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
    public function edit($subCategoryId, Request $request)
    {

        $sub_category = SubCategory::find($subCategoryId);

        $categories = Category::orderBy('name', 'ASC')->get();

        // return $categories;

        if (empty($sub_category)) {
            return redirect()->route('sub_categories.list')->with('error', 'Sub-Category does not exist.');
        }
        return view('admin.sub_category.edit', compact('sub_category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($subCategoryId, Request $request)
    {
        // Retrieve the sub-category using the given ID
        $sub_category = SubCategory::find($subCategoryId);

        if (empty($sub_category)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sub-Category not found',
            ]);
        }

        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sub_categories,slug,' . $sub_category->id,
            'is_home' => 'required|boolean',
            'status' => 'required|boolean',
            'category_id' => 'required|exists:categories,id', // Validate the category_id
        ]);

        try {
            // Only generate a new slug if the name has changed
            if ($sub_category->name != $validatedData['name']) {
                $slug = Str::slug($validatedData['name']);
                $existingSlugCount = SubCategory::where('slug', 'like', $slug . '%')
                    ->where('id', '!=', $sub_category->id)
                    ->count();

                if ($existingSlugCount > 0) {
                    $slug = $slug . '-' . ($existingSlugCount + 1);
                }
                $sub_category->slug = $slug;
            }

            // Update the sub-category data
            $sub_category->name = $validatedData['name'];
            $sub_category->category_id = $validatedData['category_id']; // Save the category_id
            $sub_category->is_home = $validatedData['is_home'];
            $sub_category->status = $validatedData['status'];
            $sub_category->save();

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Sub-Category updated successfully!',
                'data' => $sub_category,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the sub-category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($subCategoryId)
    {
        // Find the category by ID
        $sub_category = SubCategory::find($subCategoryId);

        if (empty($sub_category)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        try {


            // Delete the category
            $sub_category->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Category deleted successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the category',
                'error' => $e->getMessage(), // Include the error message for debugging
            ], 500);
        }
    }
}
