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
}