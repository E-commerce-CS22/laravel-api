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
}