<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WishListService;

class WishListController extends Controller
{
    protected $wishListService;

    public function __construct(WishListService $wishListService)
    {
        $this->wishListService = $wishListService;
    }

    public function create()
    {
        $wishListService = $this->wishListService->createWishList([]);
        return $wishListService;
    }
}