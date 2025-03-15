<?php
namespace App\Repositories;

use App\Models\Cart;

class CartRepository
{
    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }
    public function create(array $data)
    {
        return $this->cart->create($data);
    }

    public function addProduct(int $cartId, array $productData)
    {
        $cart = $this->cart->find($cartId);
        return $cart->products()->attach($productData['product_id'], ['quantity' => $productData['quantity']]);
    }

    public function deleteProduct(int $cartId, int $productId)
    {
        $cart = $this->cart->find($cartId);
        return $cart->products()->detach($productId);
    }

    public function getProducts(int $cartId)
    {
        $cart = $this->cart->find($cartId);
        return $cart->products;
    }
}