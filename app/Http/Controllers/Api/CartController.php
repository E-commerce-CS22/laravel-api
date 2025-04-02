<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function addProduct(Request $request)
    {
        $customer = Auth::user()->customer;
        if (!$customer || !$customer->cart) {
            return response()->json(['message' => 'Cart not found for the customer.'], 404);
        }
        $cartId = $customer->cart->id;
        $productData = $request->all();
        $product = $this->cartService->addProductToCart($cartId, $productData);
        return response()->json($product, 201);
    }

    public function deleteProduct($productId)
    {
        $customer = Auth::user()->customer;
        if (!$customer || !$customer->cart) {
            return response()->json(['message' => 'Cart not found for the customer.'], 404);
        }
        $cartId = $customer->cart->id;
        $this->cartService->deleteProductFromCart($cartId, $productId);
        return response()->json(null, 204);
    }

    public function showProducts()
    {
        $customer = Auth::user();
        if (!$customer || !$customer->cart) {
            return response()->json(['message' => 'Cart not found for the customer.'], 404);
        }
        $cartId = $customer->cart->id;
        $products = $this->cartService->getProductsInCart($cartId);
        return response()->json($products);
    }

    public function updateProductQuantity(Request $request, $productId)
    {
        $customer = Auth::user()->customer;
        if (!$customer || !$customer->cart) {
            return response()->json(['message' => 'Cart not found for the customer.'], 404);
        }
        $cartId = $customer->cart->id;
        $quantity = $request->input('quantity');
        $result = $this->cartService->updateProductQuantity($cartId, $productId, $quantity);
        return response()->json($result);
    }
}
