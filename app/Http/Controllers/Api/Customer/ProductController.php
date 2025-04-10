<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15); // Default to 15 items per page
        $page = $request->input('page', 1); // Default to first page
        
        $products = $this->productService->getAllProducts($perPage, $page);
        return ProductResource::collection($products);
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return new ProductResource($product);
    }
}