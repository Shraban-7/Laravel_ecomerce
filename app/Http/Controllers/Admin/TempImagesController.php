<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
// use Intervention\Image\Image;
use App\Models\TemporaryImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image;

// use Image;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->image;

        if (!empty($image)) {
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $tempImage = new TemporaryImages();
            $tempImage->name = $imageName;
            $tempImage->save();

            $image->move(public_path() . '/temp', $imageName);

            //Generate thumbnail

            $sourcePath = public_path() . '/temp/' . $imageName;
            $destinationPath = public_path() . '/temp/thumbnail/';
            $thumbnailName = 'thumb_' . $imageName;
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($sourcePath);
            $image->resize(300, null, function ($constraint) {
                $constraint->aspectRatio(); // Maintain aspect ratio
                $constraint->upsize(); // Prevent upsizing
            });

            $image->save($destinationPath . $thumbnailName);

            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'image_path' => asset('/temp/thumbnail/' . $thumbnailName),
                'message' => 'image uploaded successfully',
            ]);
        }
    }

    // Update the temporary image (replace it)
    public function update(Request $request)
    {
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $tempImagePath= $image->getPathName();

        $product_image = new ProductImage();
        $product_image->product_id = $request->product_id;
        $product_image->image = 'NULL';
        $product_image->save();

        $productImageName = $request->product_id. '-' . $product_image->id . '-' . time() . '.' . $ext;
        $product_image->image = $productImageName;
        $product_image->save();

        // Path to the temp file
        $sPath = $tempImagePath;

        // Handle the large image
        $largeImagePath = public_path() . '/uploads/product/large/' . $productImageName;

        // Create the ImageManager instance
        $manager = new ImageManager(Driver::class); // or 'imagick' depending on your setup

        // Resize the image for large image
        $largeImage = $manager->read($sPath); // Read the image
        $largeImage->resize(1400, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $largeImage->save($largeImagePath);

        // Handle the small image (thumbnail)
        $smallImagePath = public_path() . '/uploads/product/small/' . $productImageName;

        $smallImage = $manager->read($sPath); // Read the image again
        $smallImage->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $smallImage->crop(300, 300); // Crop to make it exactly 300x300
        $smallImage->save($smallImagePath);


        return response()->json([
            'status' => 'success',
            'image_id' => $product_image->id,
            'image_path' => asset('uploads/product/small/'.$product_image->image),
            'message' => 'Product updated successfully!'
        ]);
    }

    public function delete(Request $request)
    {
        // Validate the request
        $request->validate([
            'image_id' => 'required|integer|exists:temporary_images,id',
        ]);

        try {
            // Find the temporary image record
            $tempImage = TemporaryImages::findOrFail($request->input('image_id'));

            // Construct the path to the temporary image file
            $tempImagePath = public_path('temp/' . $tempImage->name);

            // Delete the image file if it exists
            if (File::exists($tempImagePath)) {
                File::delete($tempImagePath);
            }

            // Delete the record from the database
            $tempImage->delete();

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Temporary image deleted successfully!',
            ]);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the temporary image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
