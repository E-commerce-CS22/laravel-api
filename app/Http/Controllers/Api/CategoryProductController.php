<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CategoryProductController extends Controller
{
    /**
     * Get products by category with pagination
     * 
     * @param Request $request
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsByCategory(Request $request, $categoryId)
    {
        try {
            // Find the category
            $category = Category::findOrFail($categoryId);
            
            // Get pagination parameters with defaults
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            
            // Query products with active status that belong to the category
            $productsQuery = $category->products()
                ->where('status', 'active')
                ->where(function($query) {
                    $query->where('is_parent', true)
                          ->orWhereNull('parent_id');
                });
            
            // Apply sorting if specified
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $productsQuery->orderBy($sortBy, $sortOrder);
            
            // Get paginated results
            $products = $productsQuery->paginate($perPage);
            
            // Process each product to include discount information
            $products->getCollection()->transform(function ($product) {
                // Calculate discount if applicable
                $now = Carbon::now();
                $hasActiveDiscount = false;
                $finalPrice = $product->price;
                
                if ($product->discount_type && $product->discount_value && 
                    $product->discount_start_date && $product->discount_end_date) {
                    
                    $startDate = Carbon::parse($product->discount_start_date);
                    $endDate = Carbon::parse($product->discount_end_date);
                    
                    if ($now->between($startDate, $endDate)) {
                        $hasActiveDiscount = true;
                        
                        if ($product->discount_type === 'percentage') {
                            $finalPrice = $product->price - ($product->price * ($product->discount_value / 100));
                        } else {
                            $finalPrice = $product->price - $product->discount_value;
                            if ($finalPrice < 0) $finalPrice = 0;
                        }
                    }
                }
                
                // Add discount information to product
                $product->final_price = $finalPrice;
                $product->has_discount = $hasActiveDiscount;
                
                // Include primary image if available
                $primaryImage = $product->images()->where('is_primary', true)->first();
                if ($primaryImage) {
                    $product->primary_image = $primaryImage->file_path;
                }
                
                return $product;
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => [
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'image' => $category->image
                    ],
                    'products' => $products
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
