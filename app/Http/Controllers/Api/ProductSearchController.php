<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProductSearchController extends Controller
{
    /**
     * Search for products based on query string
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        // Log the search request for debugging
        Log::info('Product search request', [
            'query' => $request->all(),
            'search_term' => $request->input('q')
        ]);
        
        try {
            $query = Product::query();
            
            // Basic search by name or description
            if ($request->has('q')) {
                $searchTerm = $request->q;
                // Log the search term for debugging
                Log::info('Searching with term', ['term' => $searchTerm]);
                
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                    // Removed SKU search as the column doesn't exist
                });
            }
            
            // Pagination parameters
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            
            // Get only parent products or all products
            if ($request->has('parent_only') && $request->parent_only === 'true') {
                $query->where('is_parent', true);
            }
            
            // Include related data
            $query->with(['categories', 'tags', 'images']);
            
            // Get products with active discounts
            if ($request->has('on_sale') && $request->on_sale === 'true') {
                $now = Carbon::now()->format('Y-m-d H:i:s');
                $query->whereNotNull('discount_type')
                      ->whereNotNull('discount_value')
                      ->where('discount_start_date', '<=', $now)
                      ->where('discount_end_date', '>=', $now);
            }
            
            // Sort results
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            
            // Validate sort fields to prevent SQL injection
            $allowedSortFields = ['name', 'price', 'created_at', 'updated_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortDirection === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('created_at', 'desc'); // Default sorting
            }
            
            // Execute query with pagination
            $products = $query->paginate($perPage, ['*'], 'page', $page);
            
            // Log the results for debugging
            Log::info('Search results', [
                'total' => $products->total(),
                'current_page' => $products->currentPage()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'from' => $products->firstItem() ?? 0,
                    'last_page' => $products->lastPage(),
                    'path' => $request->url(),
                    'per_page' => $products->perPage(),
                    'to' => $products->lastItem() ?? 0,
                    'total' => $products->total(),
                ],
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error in product search', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching for products',
                'error' => $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
    
    /**
     * Alternative search method that handles special characters in URL path
     *
     * @param Request $request
     * @param string|null $query
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchGet(Request $request, $query = null)
    {
        // Merge the path parameter with the query parameters
        if ($query !== null) {
            $request->merge(['q' => $query]);
        }
        
        // Log the special search request for debugging
        Log::info('Special search request', [
            'query_path' => $query,
            'query_params' => $request->all(),
            'full_url' => $request->fullUrl()
        ]);
        
        // Use the standard search method
        return $this->search($request);
    }
    
    /**
     * Filter products by various attributes
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        // Log the filter request for debugging
        Log::info('Product filter request', [
            'filters' => $request->all()
        ]);
        
        try {
            $query = Product::query();
            
            // Filter by price range
            if ($request->has('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }
            
            if ($request->has('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }
            
            // Filter by status - only if status column exists
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by parent/child status - only if is_parent column exists
            if ($request->has('is_parent')) {
                $query->where('is_parent', $request->is_parent === 'true');
            }
            
            // Filter by parent_id - only if parent_id column exists
            if ($request->has('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            }
            
            // Filter by discount availability - only if these columns exist
            if ($request->has('has_discount')) {
                if ($request->has_discount === 'true') {
                    $query->whereNotNull('discount_type')
                          ->whereNotNull('discount_value');
                } else {
                    $query->whereNull('discount_type')
                          ->whereNull('discount_value');
                }
            }
            
            // Filter by active discount - only if these columns exist
            if ($request->has('active_discount')) {
                $now = Carbon::now()->format('Y-m-d H:i:s');
                if ($request->active_discount === 'true') {
                    $query->whereNotNull('discount_type')
                          ->whereNotNull('discount_value')
                          ->where('discount_start_date', '<=', $now)
                          ->where('discount_end_date', '>=', $now);
                } else {
                    $query->where(function($q) use ($now) {
                        $q->whereNull('discount_type')
                          ->orWhereNull('discount_value')
                          ->orWhere('discount_start_date', '>', $now)
                          ->orWhere('discount_end_date', '<', $now);
                    });
                }
            }
            
            // Filter by discount type - only if this column exists
            if ($request->has('discount_type')) {
                $query->where('discount_type', $request->discount_type);
            }
            
            // Filter by stock availability - only if stock column exists
            if ($request->has('in_stock')) {
                if ($request->in_stock === 'true') {
                    $query->where('stock', '>', 0);
                } else {
                    $query->where('stock', '<=', 0);
                }
            }
            
            // Filter by category - only if categories relationship exists
            if ($request->has('category_id')) {
                $query->whereHas('categories', function($q) use ($request) {
                    $q->where('categories.id', $request->category_id);
                });
            }
            
            // Filter by tag - only if tags relationship exists
            if ($request->has('tag_id')) {
                $query->whereHas('tags', function($q) use ($request) {
                    $q->where('tags.id', $request->tag_id);
                });
            }
            
            // Filter by creation date range
            if ($request->has('created_after')) {
                $query->where('created_at', '>=', Carbon::parse($request->created_after));
            }
            
            if ($request->has('created_before')) {
                $query->where('created_at', '<=', Carbon::parse($request->created_before));
            }
            
            // Filter by update date range
            if ($request->has('updated_after')) {
                $query->where('updated_at', '>=', Carbon::parse($request->updated_after));
            }
            
            if ($request->has('updated_before')) {
                $query->where('updated_at', '<=', Carbon::parse($request->updated_before));
            }
            
            // Include related data - only if these relationships exist
            $query->with(['categories', 'tags']);
            
            // Pagination parameters
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            
            // Sort results
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            
            // Validate sort fields to prevent SQL injection - only include columns that exist
            $allowedSortFields = ['name', 'price', 'created_at', 'updated_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortDirection === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('created_at', 'desc'); // Default sorting
            }
            
            // Execute query with pagination
            $products = $query->paginate($perPage, ['*'], 'page', $page);
            
            // Log the results for debugging
            Log::info('Filter results', [
                'total' => $products->total(),
                'current_page' => $products->currentPage()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'from' => $products->firstItem() ?? 0,
                    'last_page' => $products->lastPage(),
                    'path' => $request->url(),
                    'per_page' => $products->perPage(),
                    'to' => $products->lastItem() ?? 0,
                    'total' => $products->total(),
                ],
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error in product filter', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while filtering products',
                'error' => $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
    
    /**
     * Get all products with optional filtering
     * This method ensures products are always returned, even if filter parameters are invalid
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterAll(Request $request)
    {
        // Log the filter request for debugging
        Log::info('Product filterAll request', [
            'filters' => $request->all()
        ]);
        
        try {
            $query = Product::query();
            
            // Apply price filters if they exist
            if ($request->has('price_min') && is_numeric($request->price_min)) {
                $query->where('price', '>=', $request->price_min);
            }
            
            if ($request->has('price_max') && is_numeric($request->price_max)) {
                $query->where('price', '<=', $request->price_max);
            }
            
            // Apply date filters if they exist
            if ($request->has('created_after')) {
                try {
                    $date = Carbon::parse($request->created_after);
                    $query->where('created_at', '>=', $date);
                } catch (\Exception $e) {
                    // Invalid date format, ignore this filter
                    Log::warning('Invalid created_after date format', ['value' => $request->created_after]);
                }
            }
            
            if ($request->has('created_before')) {
                try {
                    $date = Carbon::parse($request->created_before);
                    $query->where('created_at', '<=', $date);
                } catch (\Exception $e) {
                    // Invalid date format, ignore this filter
                    Log::warning('Invalid created_before date format', ['value' => $request->created_before]);
                }
            }
            
            // Include related data if the relationships exist
            try {
                $query->with(['categories', 'tags']);
            } catch (\Exception $e) {
                // If relationships don't exist, continue without them
                Log::warning('Could not load relationships', ['error' => $e->getMessage()]);
            }
            
            // Pagination parameters
            $perPage = $request->input('per_page', 15);
            if (!is_numeric($perPage) || $perPage < 1) {
                $perPage = 15; // Default if invalid
            }
            
            $page = $request->input('page', 1);
            if (!is_numeric($page) || $page < 1) {
                $page = 1; // Default if invalid
            }
            
            // Sort results
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            
            // Only allow sorting by columns we know exist
            $allowedSortFields = ['name', 'price', 'created_at', 'updated_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortDirection === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('created_at', 'desc'); // Default sorting
            }
            
            // Execute query with pagination
            $products = $query->paginate($perPage, ['*'], 'page', $page);
            
            // Log the results
            Log::info('FilterAll results', [
                'total' => $products->total(),
                'current_page' => $products->currentPage()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'from' => $products->firstItem() ?? 0,
                    'last_page' => $products->lastPage(),
                    'path' => $request->url(),
                    'per_page' => $products->perPage(),
                    'to' => $products->lastItem() ?? 0,
                    'total' => $products->total(),
                ],
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in filterAll', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // If something goes wrong, still try to return all products
            try {
                $products = Product::paginate(15);
                
                return response()->json([
                    'success' => true,
                    'data' => ProductResource::collection($products),
                    'meta' => [
                        'current_page' => $products->currentPage(),
                        'from' => $products->firstItem() ?? 0,
                        'last_page' => $products->lastPage(),
                        'path' => $request->url(),
                        'per_page' => $products->perPage(),
                        'to' => $products->lastItem() ?? 0,
                        'total' => $products->total(),
                    ],
                    'warning' => 'Some filters were ignored due to errors: ' . $e->getMessage()
                ]);
            } catch (\Exception $fallbackError) {
                // If everything fails, return error with debugging info
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while filtering products',
                    'error' => $e->getMessage(),
                    'debug_info' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ], 500);
            }
        }
    }
}
