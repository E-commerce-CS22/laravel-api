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

    public function addProductToWishList(int $wishListId, int $productId)
    {
        $wishList = $this->wishListRepository->find($wishListId);
        if (!$wishList) {
            return response()->json(['message' => 'Wishlist not found.'], 404);
        }
        if ($wishList->products()->where('product_id', $productId)->exists()) {
            return response()->json(['message' => 'This product already exists in the wishlist.'], 400);
        }
        return $this->wishListRepository->addProduct($wishListId, $productId);
    }

    public function deleteProductFromWishList(int $wishListId, int $productId)
    {
        $wishList = $this->wishListRepository->find($wishListId);
        if (!$wishList) {
            return response()->json(['message' => 'Wishlist not found.'], 404);
        }
        return $this->wishListRepository->deleteProduct($wishListId, $productId);
    }

    public function getProductsInWishList(int $wishListId)
    {
        return $this->wishListRepository->getProducts($wishListId);
    }
}