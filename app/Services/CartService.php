<?php
namespace App\Services;

use App\Repositories\CartRepository;

class CartService
{
    protected $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }
    public function createCart(array $data)
    {
        return $this->cartRepository->create($data);
    }

    public function addProductToCart(int $cartId, array $productData)
    {
        $cart = $this->cartRepository->find($cartId);
        if ($cart->products()->where('product_id', $productData['product_id'])->exists()) {
            return response()->json(['message' => 'This product already exists in the cart.'], 400);
        }
        return $this->cartRepository->addProduct($cartId, $productData);
    }

    public function deleteProductFromCart(int $cartId, int $productId)
    {
        return $this->cartRepository->deleteProduct($cartId, $productId);
    }

    public function getProductsInCart(int $cartId)
    {
        return $this->cartRepository->getProducts($cartId);
    }

    public function updateProductQuantity(int $cartId, int $productId, int $quantity)
    {
        if ($quantity < 1) {
            return response()->json(['message' => 'Quantity must be at least 1.'], 400);
        }
        return $this->cartRepository->updateProductQuantity($cartId, $productId, $quantity);
    }
}