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

    /**
     * @OA\Get(
     *     path="/admin/products",
     *     summary="Get list of products (Admin)",
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        $products = $this->productService->getAllProducts();
        return ProductResource::collection($products);
    }

    /**
     * @OA\Post(
     *     path="/admin/products",
     *     summary="Create a new product (Admin)",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=201, description="Product created successfully")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $product = $this->productService->createProduct($data);
        return new ProductResource($product);
    }

    /**
     * @OA\Get(
     *     path="/admin/products/{id}",
     *     summary="Get a product by ID (Admin)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return new ProductResource($product);
    }

    /**
     * @OA\Put(
     *     path="/admin/products/{id}",
     *     summary="Update a product by ID (Admin)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=200, description="Product updated successfully"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $product = $this->productService->updateProduct($id, $data);
        return new ProductResource($product);
    }

    /**
     * @OA\Delete(
     *     path="/admin/products/{id}",
     *     summary="Delete a product by ID (Admin)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Product deleted successfully"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function destroy($id)
    {
        $product = $this->productService->deleteProduct($id);
        return new ProductResource($product);
    }
}
