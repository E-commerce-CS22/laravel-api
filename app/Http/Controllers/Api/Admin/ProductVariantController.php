<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Resources\ProductVariantResource;
use Illuminate\Support\Facades\Validator;

class ProductVariantController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Get all variants for a product
     * 
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($productId)
    {
        try {
            $product = $this->productService->getProductById($productId);
            
            if (!$product->is_parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only parent products can have variants'
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'data' => ProductVariantResource::collection($product->variants)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product variants',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific variant
     * 
     * @param int $productId
     * @param int $variantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($productId, $variantId)
    {
        try {
            $product = $this->productService->getProductById($productId);
            $variant = $product->variants()->findOrFail($variantId);
            
            return response()->json([
                'success' => true,
                'data' => new ProductVariantResource($variant)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product variant',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create a new variant for a product
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $productId)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'sku' => 'required|string|max:100|unique:product_variants,sku',
            'price' => 'sometimes|numeric|min:0',
            'extra_price' => 'sometimes|numeric',
            'stock' => 'sometimes|integer|min:0',
            'is_default' => 'sometimes|boolean',
            'variant_title' => 'sometimes|string|max:255',
            'attributes' => 'sometimes|array',
            'attributes.*.attribute_id' => 'required_with:attributes|exists:attributes,id',
            'attributes.*.attribute_value_id' => 'required_with:attributes|exists:attribute_values,id',
            'images' => 'sometimes|array',
            'images.*.file' => 'sometimes|file|image|max:5120',
            'images.*.alt_text' => 'sometimes|string|max:255',
            'images.*.is_primary' => 'sometimes|boolean',
            'images.*.sort_order' => 'sometimes|integer',
            'images.*.image_type' => 'sometimes|in:main,thumbnail,gallery,lifestyle',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $product = $this->productService->getProductById($productId);
            
            if (!$product->is_parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only parent products can have variants'
                ], 400);
            }
            
            $data = $request->all();
            
            // Process variant images if they exist
            if ($request->hasFile('images')) {
                $this->processImageFiles($data, $request);
            }
            
            $variant = $this->productService->createProductVariant($productId, $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Product variant created successfully',
                'data' => new ProductVariantResource($variant)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product variant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a product variant
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $productId
     * @param int $variantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $productId, $variantId)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'sku' => 'sometimes|string|max:100|unique:product_variants,sku,' . $variantId,
            'price' => 'sometimes|numeric|min:0',
            'extra_price' => 'sometimes|numeric',
            'stock' => 'sometimes|integer|min:0',
            'is_default' => 'sometimes|boolean',
            'variant_title' => 'sometimes|string|max:255',
            'attributes' => 'sometimes|array',
            'attributes.*.attribute_id' => 'required_with:attributes|exists:attributes,id',
            'attributes.*.attribute_value_id' => 'required_with:attributes|exists:attribute_values,id',
            'images' => 'sometimes|array',
            'images.*.id' => 'sometimes|exists:product_images,id',
            'images.*.file' => 'sometimes|file|image|max:5120',
            'images.*.alt_text' => 'sometimes|string|max:255',
            'images.*.is_primary' => 'sometimes|boolean',
            'images.*.sort_order' => 'sometimes|integer',
            'images.*.image_type' => 'sometimes|in:main,thumbnail,gallery,lifestyle',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $product = $this->productService->getProductById($productId);
            
            // Verify the variant belongs to the product
            $variant = $product->variants()->findOrFail($variantId);
            
            $data = $request->all();
            
            // Process variant images if they exist
            if ($request->hasFile('images')) {
                $this->processImageFiles($data, $request);
            }
            
            $variant = $this->productService->updateProductVariant($variantId, $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Product variant updated successfully',
                'data' => new ProductVariantResource($variant)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product variant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a product variant
     * 
     * @param int $productId
     * @param int $variantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($productId, $variantId)
    {
        try {
            $product = $this->productService->getProductById($productId);
            
            // Verify the variant belongs to the product
            $variant = $product->variants()->findOrFail($variantId);
            
            // Check if this is the only variant
            if ($product->variants()->count() <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the only variant of a product'
                ], 400);
            }
            
            $variant = $this->productService->deleteProductVariant($variantId);
            
            return response()->json([
                'success' => true,
                'message' => 'Product variant deleted successfully',
                'data' => new ProductVariantResource($variant)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product variant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update variant stock
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $productId
     * @param int $variantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStock(Request $request, $productId, $variantId)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $product = $this->productService->getProductById($productId);
            
            // Verify the variant belongs to the product
            $variant = $product->variants()->findOrFail($variantId);
            
            $variant = $this->productService->updateProductVariant($variantId, [
                'stock' => $request->stock
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Variant stock updated successfully',
                'data' => new ProductVariantResource($variant)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update variant stock',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set a variant as the default
     * 
     * @param int $productId
     * @param int $variantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function setDefault($productId, $variantId)
    {
        try {
            $product = $this->productService->getProductById($productId);
            
            // Verify the variant belongs to the product
            $variant = $product->variants()->findOrFail($variantId);
            
            // Set all variants to non-default
            foreach ($product->variants as $v) {
                $this->productService->updateProductVariant($v->id, [
                    'is_default' => false
                ]);
            }
            
            // Set the selected variant as default
            $variant = $this->productService->updateProductVariant($variantId, [
                'is_default' => true
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Default variant set successfully',
                'data' => new ProductVariantResource($variant)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set default variant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process image files from the request
     * 
     * @param array &$data
     * @param Request $request
     * @return void
     */
    protected function processImageFiles(&$data, Request $request)
    {
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $image) {
                if (isset($image['file']) && $request->hasFile("images.{$key}.file")) {
                    $data['images'][$key]['file'] = $request->file("images.{$key}.file");
                }
            }
        }
    }
}