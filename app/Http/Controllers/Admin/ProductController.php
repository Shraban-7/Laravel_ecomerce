<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRating;
use App\Models\TemporaryImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('product_images')->latest('id');
        if (!empty($request->get('keyword'))) {
            $keyword = $request->get('keyword');
            $query->where('title', 'like', '%' . $request->get('keyword') . '%');
        }
        $products = $query->paginate(10);
        return view('admin.products.list', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data[] = '';
        $data['categories'] = Category::orderBy('name', 'ASC')->get();
        $data['brands'] = Brand::orderBy('name', 'ASC')->get();
        // return $data;
        return view('admin.products.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request->image_array;
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products',
            'price' => 'required|numeric',
            'compare_price' => 'nullable|numeric',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'track_qty' => 'boolean',
            'qty' => 'nullable|integer',
            'is_featured' => 'boolean',
            'status' => 'required|boolean',
        ]);

        try {
            // Auto-generate a unique slug from the title
            $slug = Str::slug($validatedData['title']);
            $slugCount = Product::where('slug', 'like', $slug . '%')->count();

            if ($slugCount > 0) {
                $slug = $slug . '-' . ($slugCount + 1); // Append number if slug is not unique
            }

            // Create a new product
            $product = new Product();
            $product->title = $validatedData['title'];
            $product->slug = $slug;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->price = $validatedData['price'];
            $product->compare_price = $validatedData['compare_price'];
            $product->category_id = $validatedData['category_id'];
            $product->sub_category_id = $validatedData['sub_category_id'];
            $product->brand_id = $validatedData['brand_id'];
            $product->sku = $validatedData['sku'];
            $product->barcode = $validatedData['barcode'];
            $product->track_qty = $validatedData['track_qty'];
            $product->qty = $validatedData['qty'];
            $product->is_featured = $validatedData['is_featured'];
            $product->status = $validatedData['status'];
            $product->related_products = (!empty($request->related_products)) ? implode(',', $request->related_products) : '';
            $product->save();

            // Save image here if applicable

            if (!empty($request->image_array)) {

                foreach ($request->image_array as $key => $temp_image_id) {
                    $tempImageInfo = TemporaryImages::find($temp_image_id);
                    $extArray = explode('.', $tempImageInfo->name);
                    $ext = last($extArray);

                    $product_image = new ProductImage();
                    $product_image->product_id = $product->id;
                    $product_image->image = 'NULL';
                    $product_image->save();

                    $productImageName = $product->id . '-' . $product_image->id . '-' . time() . '.' . $ext;
                    $product_image->image = $productImageName;
                    $product_image->save();

                    // Generate product thumbnail

                    //LargeImage
                    $sPath = public_path() . '/temp/' . $tempImageInfo->name;
                    $dPath = public_path() . '/uploads/product/large/' . $productImageName;
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($sPath);
                    $image->resize(1400, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    $image->save($dPath);

                    //SmallImage
                    $dPath = public_path() . '/uploads/product/small/' . $productImageName;
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($sPath);
                    $image->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $image->crop(300, 300);

                    $image->save($dPath);
                }

            }

            // Return a success JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Product saved successfully!',
                'data' => $product,
            ]);

        } catch (\Exception $e) {
            // Catch and handle any errors
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving the product',
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
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);

        $relatedProducts = [];

        if ($product->related_products != '') {
            $productArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)->get();
        }
        $data[] = '';
        $data['categories'] = Category::orderBy('name', 'ASC')->get();
        $data['brands'] = Brand::orderBy('name', 'ASC')->get();
        $data['product'] = $product;
        $data['relatedProducts'] = $relatedProducts;
        $data['product_images'] = ProductImage::where('product_id', $product->id)->get();
        return view('admin.products.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        // Validate the input data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $id,
            'price' => 'required|numeric',
            'compare_price' => 'nullable|numeric',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'track_qty' => 'boolean',
            'qty' => 'nullable|integer',
            'is_featured' => 'boolean',
            'status' => 'required|boolean',

        ]);

        try {
            // Find the product by ID
            $product = Product::findOrFail($id);

            if ($product->title != $validatedData['title']) {
                $slug = Str::slug($validatedData['title']);
                $existingSlugCount = Product::where('slug', 'like', $slug . '%')
                    ->where('id', '!=', $product->id)
                    ->count();

                if ($existingSlugCount > 0) {
                    $slug = $slug . '-' . ($existingSlugCount + 1);
                }
                $product->slug = $slug;
            }

            // return $request->all();

            // Update product details

            $product->title = $validatedData['title'];
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->price = $validatedData['price'];
            $product->compare_price = $validatedData['compare_price'];
            $product->category_id = $validatedData['category_id'];
            $product->sub_category_id = $validatedData['sub_category_id'];
            $product->brand_id = $validatedData['brand_id'];
            $product->sku = $validatedData['sku'];
            $product->barcode = $validatedData['barcode'];
            $product->track_qty = $validatedData['track_qty'];
            $product->qty = $validatedData['qty'];
            $product->is_featured = $validatedData['is_featured'];
            $product->related_products = (!empty($request->related_products)) ? implode(',', $request->related_products) : '';
            $product->status = $validatedData['status'];

            // Handle the removal of images if any
            if ($request->has('remove_images')) {
                $removeImages = $request->input('remove_images', []);
                foreach ($removeImages as $imageId) {
                    // Find and delete the image
                    $productImage = ProductImage::find($imageId);
                    if ($productImage) {
                        // Ensure the correct paths to images are used
                        $largeImagePath = public_path('uploads/product/large/' . $productImage->image);
                        $smallImagePath = public_path('uploads/product/small/' . $productImage->image);

                        // Delete the large and small images if they exist
                        if (File::exists($largeImagePath)) {
                            File::delete($largeImagePath);
                        }
                        if (File::exists($smallImagePath)) {
                            File::delete($smallImagePath);
                        }

                        // Remove the image record from the database
                        $productImage->delete();
                    }
                }
            }

            $product->save();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully!',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($productId, Request $request)
    {
        // Find the product by ID
        $product = Product::find($productId);

        if (empty($product)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $productImages = ProductImage::where('product_id', $productId)->get();

        try {
            // Delete associated image files if they exist
            // Assuming a relationship with ProductImage
            foreach ($productImages as $productImage) {
                $largeImagePath = public_path('uploads/product/large/' . $productImage->image);
                $smallImagePath = public_path('uploads/product/small/' . $productImage->image);

                if (File::exists($largeImagePath)) {
                    File::delete($largeImagePath);
                }

                if (File::exists($smallImagePath)) {
                    File::delete($smallImagePath);
                }

                // Delete the image record from the database
                $productImage->delete();
            }

            // Delete the product itself
            $product->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the product',
                'error' => $e->getMessage(), // Include the error message for debugging
            ], 500);
        }
    }

    public function getRelatedProducts(Request $request)
    {

        $tempProduct = [];
        if ($request->term != " ") {
            $products = Product::where('title', 'LIKE', '%' . $request->term . '%')->get();
            if ($products != null) {
                foreach ($products as $key => $product) {
                    $tempProduct[] = array('id' => $product->id, 'text' => $product->title);
                }
            }
        }

        return response()->json([
            "tags" => $tempProduct,
            "status" => true,
        ]);
    }

    public function product_rating()
    {
      $ratings = ProductRating::with('product') // Load the related product for each rating
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.products.ratings',compact('ratings'));
    }

    public function updateProductStatus(Request $request, $id)
{
    $rating = ProductRating::find($id);

    if (!$rating) {
        return response()->json(['status' => 'error', 'message' => 'Rating not found.'], 404);
    }

    $rating->status = $request->status;
    $rating->save();

    return response()->json(['status' => 'success', 'message' => 'Rating status updated successfully.']);
}


}
