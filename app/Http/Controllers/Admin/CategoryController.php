<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TemporaryImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::latest(); // Start with the query builder

        // Apply the search filter if a keyword is provided
        if (!empty($request->get('keyword'))) {
            $query->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        // Paginate the results
        $categories = $query->paginate(10);

        // Pass the categories to the view
        return view('admin.category.list', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories',
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
            $category = new Category();
            $category->name = $validatedData['name'];
            $category->slug = $slug;
            $category->is_home = $validatedData['is_home'];
            $category->status = $validatedData['status'];
            $category->save();

            //Sava image here

            if (!empty($request->image_id)) {
                $tempImage = TemporaryImages::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);

                // Generate image thumbnail
                $thumbnail = public_path() . '/uploads/category/thumbnail/' . $newImageName;
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sPath);
                $image->save($thumbnail);

                $category->image = $newImageName;
                $category->save();
            }

            // Return a success JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Category saved successfully!',
                'data' => $category,
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

    public function show()
    {

    }

    public function edit($categoryId, Request $request)
    {
        $category = Category::find($categoryId);

        if (empty($category)) {
            return redirect()->route('categories.list')->with('error', 'Category does not exist.');
        }
        return view('admin.category.edit', compact('category'));
    }

    public function update($categoryId, Request $request)
    {
        // Retrieve the category using the given ID
        $category = Category::find($categoryId);

        if (empty($category)) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ]);
        }

        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'is_home' => 'required|boolean',
            'status' => 'required|boolean',
        ]);

        try {
            // Only generate a new slug if the name has changed
            if ($category->name != $validatedData['name']) {
                $slug = Str::slug($validatedData['name']);
                $existingSlugCount = Category::where('slug', 'like', $slug . '%')
                    ->where('id', '!=', $category->id)
                    ->count();

                if ($existingSlugCount > 0) {
                    $slug = $slug . '-' . ($existingSlugCount + 1);
                }
                $category->slug = $slug;
            }

            // Update the category data
            $category->name = $validatedData['name'];
            $category->is_home = $validatedData['is_home'];
            $category->status = $validatedData['status'];

            // Check if an image is being updated
            if (!empty($request->image_id)) {
                // Delete the old image if it exists
                if (!empty($category->image)) {
                    $oldImagePath = public_path('uploads/category/' . $category->image);
                    $oldThumbnailPath = public_path('uploads/category/thumbnail/' . $category->image);

                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath); // Delete the old image
                    }

                    if (File::exists($oldThumbnailPath)) {
                        File::delete($oldThumbnailPath); // Delete the old thumbnail
                    }
                }

                // Save the new image
                $tempImage = TemporaryImages::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);

                // Generate thumbnail for the new image
                $thumbnail = public_path() . '/uploads/category/thumbnail/' . $newImageName;
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sPath);
                $image->resize(450, 600);
                $image->save($thumbnail);

                // Update the category image field
                $category->image = $newImageName;
            }

            // Save the category
            $category->save();

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Category updated successfully!',
                'data' => $category,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($categoryId)
    {
        // Find the category by ID
        $category = Category::find($categoryId);

        if (empty($category)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        try {
            // Delete associated image files if they exist
            if (!empty($category->image)) {
                $imagePath = public_path('uploads/category/' . $category->image);
                $thumbnailPath = public_path('uploads/category/thumbnail/' . $category->image);

                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }

                if (File::exists($thumbnailPath)) {
                    File::delete($thumbnailPath);
                }
            }

            // Delete the category
            $category->delete();

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
