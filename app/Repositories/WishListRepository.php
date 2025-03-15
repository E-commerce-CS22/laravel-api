<?php
namespace App\Repositories;

use App\Models\WishList;

class WishListRepository
{
    protected $wishList;

    public function __construct(WishList $wishList)
    {
        $this->wishList = $wishList;
    }
    public function create(array $data)
    {
        return $this->wishList->create($data);
    }   

    public function addProduct(int $wishListId, int $productId)
    {
        $wishList = $this->wishList->find($wishListId);
        return $wishList->products()->attach($productId);
    }

    public function deleteProduct(int $wishListId, int $productId)
    {
        $wishList = $this->wishList->find($wishListId);
        return $wishList->products()->detach($productId);
    }

    public function find(int $wishListId)
    {
        return $this->wishList->find($wishListId);
    }

    public function getProducts(int $wishListId)
    {
        $wishList = $this->wishList->find($wishListId);
        return $wishList->products;
    }
}