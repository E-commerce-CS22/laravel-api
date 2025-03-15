<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WishListService;
use Illuminate\Http\Request;

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

    public function addProduct($wishListId, $productId)
    {
        $product = $this->wishListService->addProductToWishList($wishListId, $productId);
        return response()->json($product, 201);
    }

    public function deleteProduct($wishListId, $productId)
    {
        $this->wishListService->deleteProductFromWishList($wishListId, $productId);
        return response()->json(null, 204);
    }

    public function showProducts($wishListId)
    {
        $products = $this->wishListService->getProductsInWishList($wishListId);
        return response()->json($products);
    }
}