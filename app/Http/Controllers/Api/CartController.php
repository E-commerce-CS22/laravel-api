<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function create()
    {
        $cart = $this->cartService->createCart([]);
        return $cart;
    }

    public function addProduct(Request $request, $cartId)
    {
        $productData = $request->all();
        $product = $this->cartService->addProductToCart($cartId, $productData);
        return response()->json($product, 201);
    }

    public function deleteProduct($cartId, $productId)
    {
        $this->cartService->deleteProductFromCart($cartId, $productId);
        return response()->json(null, 204);
    }

    public function showProducts($cartId)
    {
        $products = $this->cartService->getProductsInCart($cartId);
        return response()->json($products);
    }

    public function updateProductQuantity(Request $request, $cartId, $productId)
    {
        $quantity = $request->input('quantity');
        $result = $this->cartService->updateProductQuantity($cartId, $productId, $quantity);
        return response()->json($result);
    }
}
