<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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

    public function store(Request $request)
    {
        $data = $request->all();
        $product = $this->productService->createProduct($data);
        return new ProductResource($product);
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return new ProductResource($product);
    }


    public function update(Request $request, $id)
    {
        $data = $request->all();
        $product = $this->productService->updateProduct($id, $data);
        return new ProductResource($product);
    }

    public function destroy($id)
    {
        $product = $this->productService->deleteProduct($id);
        return new ProductResource($product);
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
