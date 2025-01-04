<?php

namespace App\Http\Controllers\Api\Admin;

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
}
