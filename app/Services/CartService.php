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
}