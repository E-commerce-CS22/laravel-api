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

    public function index()
    {
        $products = $this->productService->getAllProducts();
        return ProductResource::collection($products);
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return new ProductResource($product);
    }
}