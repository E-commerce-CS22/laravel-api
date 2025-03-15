<?php
namespace App\Services;

use App\Repositories\WishListRepository;

class WishListService
{
    protected $wishListRepository;


    public function __construct(WishListRepository $wishListRepository)
    {
        $this->wishListRepository = $wishListRepository;
    }
    public function createWishList(array $data)
    {
        return $this->wishListRepository->create($data);
    }
}