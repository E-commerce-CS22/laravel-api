<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WishListService;
use Illuminate\Support\Facades\Auth;

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

    public function addProduct($productId)
    {
        $customer = Auth::user();
        if (!$customer || !$customer->wishList) {
            return response()->json(['message' => 'Wishlist not found for the customer.'], 404);
        }
        $wishListId = $customer->wishList->id;
        $product = $this->wishListService->addProductToWishList($wishListId, $productId);
        return response()->json($product, 201);
    }

    public function deleteProduct($productId)
    {
        $customer = Auth::user();
        if (!$customer || !$customer->wishList) {
            return response()->json(['message' => 'Wishlist not found for the customer.'], 404);
        }
        $wishListId = $customer->wishList->id;
        $this->wishListService->deleteProductFromWishList($wishListId, $productId);
        return response()->json(null, 204);
    }

    public function showProducts()
    {
        $customer = Auth::user();
        if (!$customer || !$customer->wishList) {
            return response()->json(['message' => 'Wishlist not found for the customer.'], 404);
        }
        $wishListId = $customer->wishList->id;
        $products = $this->wishListService->getProductsInWishList($wishListId);
        return response()->json($products);
    }
}