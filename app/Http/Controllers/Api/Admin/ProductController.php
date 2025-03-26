<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getAllProducts();
        return ProductResource::collection($products);
    }

    public function parentProducts()
    {
        $products = $this->productService->getParentProducts();
        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_parent' => 'sometimes|boolean',
            'parent_id' => 'nullable|exists:products,id',
            'status' => 'sometimes|in:active,inactive',
            'categories' => 'sometimes|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id',
            'images' => 'sometimes|array',
            'images.*.file' => 'sometimes|file|image|max:5120', // 5MB max
            'images.*.alt_text' => 'sometimes|string|max:255',
            'images.*.is_primary' => 'sometimes|boolean',
            'images.*.sort_order' => 'sometimes|integer',
            'images.*.image_type' => 'sometimes|in:main,thumbnail,gallery,lifestyle',
            'variants' => 'sometimes|array',
            'variants.*.sku' => 'required_with:variants|string|max:100|unique:product_variants,sku',
            'variants.*.price' => 'sometimes|numeric|min:0',
            'variants.*.extra_price' => 'sometimes|numeric',
            'variants.*.stock' => 'sometimes|integer|min:0',
            'variants.*.is_default' => 'sometimes|boolean',
            'variants.*.variant_title' => 'sometimes|string|max:255',
            'variants.*.attributes' => 'sometimes|array',
            'variants.*.attributes.*.attribute_id' => 'required_with:variants.*.attributes|exists:attributes,id',
            'variants.*.attributes.*.attribute_value_id' => 'required_with:variants.*.attributes|exists:attribute_values,id',
            'variants.*.images' => 'sometimes|array',
            'variants.*.images.*.file' => 'sometimes|file|image|max:5120',
            'variants.*.images.*.alt_text' => 'sometimes|string|max:255',
            'variants.*.images.*.is_primary' => 'sometimes|boolean',
            'variants.*.images.*.sort_order' => 'sometimes|integer',
            'variants.*.images.*.image_type' => 'sometimes|in:main,thumbnail,gallery,lifestyle',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $data = $request->all();
            
            // Process product images if they exist
            if ($request->hasFile('images')) {
                $this->processImageFiles($data, $request);
            }
            
            // Process variant images if they exist
            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $key => $variant) {
                    if (isset($variant['images']) && is_array($variant['images'])) {
                        foreach ($variant['images'] as $imageKey => $image) {
                            if (isset($image['file']) && $request->hasFile("variants.{$key}.images.{$imageKey}.file")) {
                                $data['variants'][$key]['images'][$imageKey]['file'] = $request->file("variants.{$key}.images.{$imageKey}.file");
                            }
                        }
                    }
                }
            }
            
            $product = $this->productService->createProduct($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => new ProductResource($product)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = $this->productService->getProductById($id);
            return response()->json([
                'success' => true,
                'data' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'is_parent' => 'sometimes|boolean',
            'parent_id' => 'nullable|exists:products,id',
            'status' => 'sometimes|in:active,inactive',
            'categories' => 'sometimes|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id',
            'images' => 'sometimes|array',
            'images.*.id' => 'sometimes|exists:product_images,id',
            'images.*.file' => 'sometimes|file|image|max:5120', // 5MB max
            'images.*.alt_text' => 'sometimes|string|max:255',
            'images.*.is_primary' => 'sometimes|boolean',
            'images.*.sort_order' => 'sometimes|integer',
            'images.*.image_type' => 'sometimes|in:main,thumbnail,gallery,lifestyle',
            'variants' => 'sometimes|array',
            'variants.*.id' => 'sometimes|exists:product_variants,id',
            'variants.*.sku' => 'sometimes|string|max:100',
            'variants.*.price' => 'sometimes|numeric|min:0',
            'variants.*.extra_price' => 'sometimes|numeric',
            'variants.*.stock' => 'sometimes|integer|min:0',
            'variants.*.is_default' => 'sometimes|boolean',
            'variants.*.variant_title' => 'sometimes|string|max:255',
            'variants.*.attributes' => 'sometimes|array',
            'variants.*.attributes.*.attribute_id' => 'required_with:variants.*.attributes|exists:attributes,id',
            'variants.*.attributes.*.attribute_value_id' => 'required_with:variants.*.attributes|exists:attribute_values,id',
            'variants.*.images' => 'sometimes|array',
            'variants.*.images.*.id' => 'sometimes|exists:product_images,id',
            'variants.*.images.*.file' => 'sometimes|file|image|max:5120',
            'variants.*.images.*.alt_text' => 'sometimes|string|max:255',
            'variants.*.images.*.is_primary' => 'sometimes|boolean',
            'variants.*.images.*.sort_order' => 'sometimes|integer',
            'variants.*.images.*.image_type' => 'sometimes|in:main,thumbnail,gallery,lifestyle',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $data = $request->all();
            
            // Process product images if they exist
            if ($request->hasFile('images')) {
                $this->processImageFiles($data, $request);
            }
            
            // Process variant images if they exist
            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $key => $variant) {
                    if (isset($variant['images']) && is_array($variant['images'])) {
                        foreach ($variant['images'] as $imageKey => $image) {
                            if (isset($image['file']) && $request->hasFile("variants.{$key}.images.{$imageKey}.file")) {
                                $data['variants'][$key]['images'][$imageKey]['file'] = $request->file("variants.{$key}.images.{$imageKey}.file");
                            }
                        }
                    }
                }
            }
            
            $product = $this->productService->updateProduct($id, $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = $this->productService->deleteProduct($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
                'data' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
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
    
    /**
     * Get product summary information for admin before updating
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductSummary($id)
    {
        try {
            $product = $this->productService->getProductById($id);
            
            // Check if product has an active discount
            $hasActiveDiscount = false;
            $discountStatus = 'No discount';
            
            if ($product->discount_type && $product->discount_value) {
                $now = Carbon::now();
                $startDate = $product->discount_start_date ? Carbon::parse($product->discount_start_date) : null;
                $endDate = $product->discount_end_date ? Carbon::parse($product->discount_end_date) : null;
                
                if ($startDate && $endDate) {
                    if ($now->between($startDate, $endDate)) {
                        $hasActiveDiscount = true;
                        $discountStatus = 'Active';
                    } elseif ($now->lt($startDate)) {
                        $hasActiveDiscount = false;
                        $discountStatus = 'Scheduled (Future)';
                    } elseif ($now->gt($endDate)) {
                        $hasActiveDiscount = false;
                        $discountStatus = 'Expired';
                    }
                }
            }
            
            // Format discount information
            $discountInfo = null;
            if ($product->discount_type && $product->discount_value) {
                $discountInfo = [
                    'type' => $product->discount_type,
                    'value' => $product->discount_value,
                    'formatted' => $product->discount_type === 'percentage' 
                        ? "{$product->discount_value}%" 
                        : "\${$product->discount_value}",
                    'start_date' => $product->discount_start_date,
                    'end_date' => $product->discount_end_date,
                    'status' => $discountStatus,
                    'is_active' => $hasActiveDiscount
                ];
            }
            
            // Calculate final price after discount if applicable
            $finalPrice = $product->price;
            if ($hasActiveDiscount) {
                if ($product->discount_type === 'percentage') {
                    $finalPrice = $product->price - ($product->price * ($product->discount_value / 100));
                } else {
                    $finalPrice = $product->price - $product->discount_value;
                    if ($finalPrice < 0) $finalPrice = 0;
                }
            }
            
            // Get product categories
            $categories = $product->categories ? $product->categories->pluck('name') : [];
            
            // Get product tags
            $tags = $product->tags ? $product->tags->pluck('name') : [];
            
            // Build summary response
            $summary = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => [
                    'original' => $product->price,
                    'final' => $finalPrice,
                    'has_discount' => $hasActiveDiscount
                ],
                'discount' => $discountInfo,
                'status' => $product->status,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ];
            
            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product summary',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function applyDiscount(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'discount_start_date' => 'required|date',
            'discount_end_date' => 'required|date|after:discount_start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Additional validation for percentage discount (must be between 0 and 100)
        if ($request->discount_type === 'percentage' && ($request->discount_value < 0 || $request->discount_value > 100)) {
            return response()->json([
                'success' => false,
                'message' => 'Percentage discount must be between 0 and 100',
            ], 400);
        }

        // Format dates to MySQL compatible format
        try {
            $startDate = Carbon::parse($request->discount_start_date)->format('Y-m-d H:i:s');
            $endDate = Carbon::parse($request->discount_end_date)->format('Y-m-d H:i:s');
            
            // Validate that start date is today or in the future
            if (Carbon::parse($startDate)->startOfDay() < Carbon::now()->startOfDay()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Start date must be today or a future date',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format',
                'error' => $e->getMessage()
            ], 400);
        }

        // Apply discount to product
        $discountData = [
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'discount_start_date' => $startDate,
            'discount_end_date' => $endDate
        ];
        
        $product = $this->productService->applyDiscount($id, $discountData);
        
        return response()->json([
            'success' => true,
            'message' => 'Discount applied successfully',
            'data' => new ProductResource($product)
        ]);
    }

    public function updateDiscount(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'discount_type' => 'sometimes|required|in:percentage,fixed',
            'discount_value' => 'sometimes|required|numeric|min:0',
            'discount_start_date' => 'sometimes|required|date',
            'discount_end_date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Get the product
        $product = $this->productService->getProductById($id);
        
        // Prepare discount data with only the fields that are provided
        $discountData = [];
        
        if (isset($request->discount_type)) {
            $discountData['discount_type'] = $request->discount_type;
            
            // If type is set but no value is provided, keep the existing value
            if (!isset($request->discount_value) && $product->discount_value) {
                $discountData['discount_value'] = $product->discount_value;
            }
        }
        
        if (isset($request->discount_value)) {
            $discountData['discount_value'] = $request->discount_value;
            
            // Additional validation for percentage discount (must be between 0 and 100)
            $discountType = isset($request->discount_type) ? $request->discount_type : $product->discount_type;
            if ($discountType === 'percentage' && ($request->discount_value < 0 || $request->discount_value > 100)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Percentage discount must be between 0 and 100',
                ], 400);
            }
            
            // If value is set but no type is provided, keep the existing type or default to percentage
            if (!isset($request->discount_type)) {
                $discountData['discount_type'] = $product->discount_type ?? 'percentage';
            }
        }
        
        // Format dates if provided
        try {
            // Handle start date
            if (isset($request->discount_start_date)) {
                $startDate = Carbon::parse($request->discount_start_date)->format('Y-m-d H:i:s');
                $discountData['discount_start_date'] = $startDate;
            } else if (!$product->discount_start_date && !isset($request->discount_start_date) && 
                      (isset($request->discount_type) || isset($request->discount_value) || isset($request->discount_end_date))) {
                // If applying a new discount and no start date specified, default to today
                $discountData['discount_start_date'] = Carbon::now()->format('Y-m-d H:i:s');
            }
            
            // Handle end date
            if (isset($request->discount_end_date)) {
                $endDate = Carbon::parse($request->discount_end_date)->format('Y-m-d H:i:s');
                $discountData['discount_end_date'] = $endDate;
            } else if (!$product->discount_end_date && !isset($request->discount_end_date) && 
                      (isset($request->discount_type) || isset($request->discount_value) || isset($request->discount_start_date))) {
                // If applying a new discount and no end date specified, default to 30 days from start date
                $startDate = isset($discountData['discount_start_date']) 
                    ? Carbon::parse($discountData['discount_start_date']) 
                    : Carbon::parse($product->discount_start_date ?? Carbon::now());
                
                $discountData['discount_end_date'] = $startDate->copy()->addDays(30)->format('Y-m-d H:i:s');
            }
            
            // Validate dates if both are present (either from request or defaults)
            $finalStartDate = $discountData['discount_start_date'] ?? $product->discount_start_date;
            $finalEndDate = $discountData['discount_end_date'] ?? $product->discount_end_date;
            
            if ($finalStartDate && $finalEndDate) {
                if (Carbon::parse($finalEndDate) <= Carbon::parse($finalStartDate)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'End date must be after start date',
                    ], 400);
                }
                
                // Validate that start date is today or in the future if it's a new discount
                if (!$product->discount_start_date && Carbon::parse($finalStartDate)->startOfDay() < Carbon::now()->startOfDay()) {
                    $discountData['discount_start_date'] = Carbon::now()->format('Y-m-d H:i:s');
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format',
                'error' => $e->getMessage()
            ], 400);
        }
        
        // If we're updating an existing discount, ensure we have all required fields
        if ($product->discount_type || $product->discount_value || $product->discount_start_date || $product->discount_end_date) {
            // Keep existing values for fields not provided
            if (!isset($discountData['discount_type']) && $product->discount_type) {
                $discountData['discount_type'] = $product->discount_type;
            }
            
            if (!isset($discountData['discount_value']) && $product->discount_value) {
                $discountData['discount_value'] = $product->discount_value;
            }
            
            if (!isset($discountData['discount_start_date']) && $product->discount_start_date) {
                $discountData['discount_start_date'] = $product->discount_start_date;
            }
            
            if (!isset($discountData['discount_end_date']) && $product->discount_end_date) {
                $discountData['discount_end_date'] = $product->discount_end_date;
            }
        }
        
        // Ensure all required fields are present
        if (isset($discountData['discount_type']) || isset($discountData['discount_value']) || 
            isset($discountData['discount_start_date']) || isset($discountData['discount_end_date'])) {
            
            // If any discount field is set, ensure all are set
            if (!isset($discountData['discount_type'])) {
                $discountData['discount_type'] = 'percentage'; // Default to percentage
            }
            
            if (!isset($discountData['discount_value'])) {
                $discountData['discount_value'] = 10; // Default to 10% or $10
            }
            
            if (!isset($discountData['discount_start_date'])) {
                $discountData['discount_start_date'] = Carbon::now()->format('Y-m-d H:i:s');
            }
            
            if (!isset($discountData['discount_end_date'])) {
                $discountData['discount_end_date'] = Carbon::parse($discountData['discount_start_date'])->addDays(30)->format('Y-m-d H:i:s');
            }
        }
        
        // Update discount
        $product = $this->productService->applyDiscount($id, $discountData);
        
        return response()->json([
            'success' => true,
            'message' => 'Discount updated successfully',
            'data' => new ProductResource($product)
        ]);
    }

    public function removeDiscount($id)
    {
        $product = $this->productService->removeDiscount($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Discount removed successfully',
            'data' => new ProductResource($product)
        ]);
    }
}
