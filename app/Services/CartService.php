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
}