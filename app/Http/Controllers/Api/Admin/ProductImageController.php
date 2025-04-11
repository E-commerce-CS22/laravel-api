<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Upload images to a product
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadProductImages(Request $request, $id)
    {

        // Validate request
        $validator = Validator::make($request->all(), [
            'images' => 'required',
            'images.*.file' => 'required|file|image|max:5120', // 5MB max
            'images.*.alt_text' => 'sometimes|string|max:255',
            'images.*.is_primary' => 'sometimes|boolean',
            'images.*.sort_order' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Find the product
            $product = Product::findOrFail($id);
            
            // Process and save images
            $uploadedImages = [];
            
            foreach ($request->file('images') as $key => $image) {
                $path = $image['file']->store('products/' . $product->id, 'public');
                
                $imageData = [
                    'product_id' => $product->id,
                    'image' => $path,
                    'alt_text' => $image['alt_text'] ?? $product->name,
                    'is_primary' => $image['is_primary'] ?? false,
                    'sort_order' => $image['sort_order'] ?? 0,
                ];
                
                $productImage = ProductImage::create($imageData);
                $uploadedImages[] = $productImage;
            }
            
            return response()->json([
                'success' => true,
                'message' => count($uploadedImages) . ' images uploaded successfully',
                'data' => $uploadedImages
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a single image to a product variant
     * 
     * @param Request $request
     * @param int $productId
     * @param int $variantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadVariantImages(Request $request, $productId, $variantId)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
            'alt_text' => 'sometimes|string|max:255',
            'is_primary' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Find the product and variant
            $product = Product::findOrFail($productId);
            $variant = ProductVariant::findOrFail($variantId);
            
            // Verify that variant belongs to product
            if ($variant->product_id != $productId) {
                return response()->json([
                    'success' => false,
                    'message' => 'This variant does not belong to the specified product'
                ], 400);
            }
            
            // Process the image string
            $imageString = $request->input('image');
            
            // Generate a unique filename
            $filename = uniqid() . '.jpg';
            $directory = 'products/' . $product->id . '/variants/' . $variant->id;
            $path = $directory . '/' . $filename;
            
            // Ensure the directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
            
            // Store the image
            Storage::disk('public')->put($path, $imageString);
            
            // Create image record
            $imageData = [
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'image' => $path,
                'alt_text' => $request->input('alt_text') ?? ($variant->variant_title ?? $product->name),
                'is_primary' => $request->input('is_primary') ?? false,
                'sort_order' => $request->input('sort_order') ?? 0,
            ];
            
            $productImage = ProductImage::create($imageData);
            
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => $productImage
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a product image
     * 
     * @param int $productId
     * @param int $imageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImage($productId, $imageId)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            // Find the image
            $image = ProductImage::findOrFail($imageId);
            
            // Check if image belongs to the product
            if ($image->product_id != $productId) {
                return response()->json([
                    'success' => false,
                    'message' => 'This image does not belong to the specified product'
                ], 400);
            }
            
            // Delete the file from storage
            if (Storage::exists($image->image)) {
                Storage::delete($image->image);
            }
            
            // Delete the image record
            $image->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
