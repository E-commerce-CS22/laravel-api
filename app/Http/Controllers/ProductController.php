<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15); // Default to 15 items per page
        $page = $request->input('page', 1); // Default to first page
        
        $products = Product::with(['tags:name,slug'])->paginate($perPage, ['*'], 'page', $page);
        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $product = Product::create($request->all());

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    public function show($id)
    {
        $product = Product::with(['tags:id,name,slug'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product, 200);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
        ]);

        $product->update($request->all());

        return response()->json(['message' => 'Product updated successfully', 'product' => $product], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->categories()->detach();
        $product->tags()->detach();
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    public function getDiscount($id)
    {
        $product = Product::findOrFail($id);

        if (!$product->discount_type || !$product->discount_value || 
            !$product->discount_start_date || !$product->discount_end_date) {
            return response()->json([
                'success' => false,
                'message' => 'No discount found for this product',
            ], 404);
        }

        // Check if the discount is still valid
        $now = Carbon::now();
        $startDate = Carbon::parse($product->discount_start_date);
        $endDate = Carbon::parse($product->discount_end_date);
        $isActive = $now->between($startDate, $endDate);

        $discountInfo = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'discount_type' => $product->discount_type,
            'discount_value' => $product->discount_value,
            'discount_start_date' => $product->discount_start_date,
            'discount_end_date' => $product->discount_end_date,
            'is_active' => $isActive,
            'status' => $isActive ? 'Active' : ($now < $startDate ? 'Scheduled' : 'Expired')
        ];

        // Calculate the final price after discount
        if ($isActive) {
            $originalPrice = $product->price;
            $finalPrice = $originalPrice;

            if ($product->discount_type === 'percentage') {
                $discountAmount = $originalPrice * ($product->discount_value / 100);
                $finalPrice = $originalPrice - $discountAmount;
            } else { // fixed amount
                $finalPrice = $originalPrice - $product->discount_value;
                // Ensure final price is not negative
                $finalPrice = max(0, $finalPrice);
            }

            $discountInfo['original_price'] = $originalPrice;
            $discountInfo['final_price'] = $finalPrice;
            $discountInfo['discount_amount'] = $originalPrice - $finalPrice;
        }

        return response()->json([
            'success' => true,
            'message' => 'Discount information retrieved successfully',
            'data' => $discountInfo
        ]);
    }

    public function applyDiscount(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
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

        // Find the product
        $product = Product::findOrFail($id);

        // Set default dates if not provided
        $startDate = $request->filled('discount_start_date') 
            ? Carbon::parse($request->discount_start_date) 
            : Carbon::now();

        $endDate = $request->filled('discount_end_date') 
            ? Carbon::parse($request->discount_end_date) 
            : $startDate->copy()->addDays(30);

        // Format dates to MySQL compatible format
        $startDateFormatted = $startDate->format('Y-m-d H:i:s');
        $endDateFormatted = $endDate->format('Y-m-d H:i:s');

        // Apply discount to product
        $product->discount_type = $request->discount_type;
        $product->discount_value = $request->discount_value;
        $product->discount_start_date = $startDateFormatted;
        $product->discount_end_date = $endDateFormatted;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Discount applied successfully',
            'data' => $product
        ]);
    }

    public function updateDiscount(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'discount_type' => 'sometimes|required|in:percentage,fixed',
            'discount_value' => 'sometimes|required|numeric|min:0',
            'discount_start_date' => 'sometimes|nullable|date',
            'discount_end_date' => 'sometimes|nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Get the product
        $product = Product::findOrFail($id);

        // Check if product has an existing discount
        if (!$product->discount_type && !$product->discount_value && 
            !$product->discount_start_date && !$product->discount_end_date) {
            return response()->json([
                'success' => false,
                'message' => 'No discount found to update. Please use the apply discount endpoint instead.',
            ], 404);
        }

        // Update discount type if provided
        if ($request->filled('discount_type')) {
            $product->discount_type = $request->discount_type;
        }

        // Update discount value if provided
        if ($request->filled('discount_value')) {
            // Additional validation for percentage discount
            if ($product->discount_type === 'percentage' && ($request->discount_value < 0 || $request->discount_value > 100)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Percentage discount must be between 0 and 100',
                ], 400);
            }

            $product->discount_value = $request->discount_value;
        }

        // Update start date if provided
        if ($request->filled('discount_start_date')) {
            $product->discount_start_date = Carbon::parse($request->discount_start_date)->format('Y-m-d H:i:s');
        }

        // Update end date if provided
        if ($request->filled('discount_end_date')) {
            $product->discount_end_date = Carbon::parse($request->discount_end_date)->format('Y-m-d H:i:s');
        }

        // Validate that end date is after start date
        if (Carbon::parse($product->discount_end_date) <= Carbon::parse($product->discount_start_date)) {
            return response()->json([
                'success' => false,
                'message' => 'End date must be after start date',
            ], 400);
        }

        // Save the updated product
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Discount updated successfully',
            'data' => $product
        ]);
    }

    public function removeDiscount($id)
    {
        // Get the product
        $product = Product::findOrFail($id);

        // Check if product has an existing discount
        if (!$product->discount_type && !$product->discount_value && 
            !$product->discount_start_date && !$product->discount_end_date) {
            return response()->json([
                'success' => false,
                'message' => 'No discount found to remove.',
            ], 404);
        }

        // Remove discount data
        $product->discount_type = null;
        $product->discount_value = null;
        $product->discount_start_date = null;
        $product->discount_end_date = null;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Discount removed successfully',
            'data' => $product
        ]);
    }
}