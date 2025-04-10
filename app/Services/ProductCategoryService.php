<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductCategoryRepository;

class ProductCategoryService
{
    protected $productRepository;
    protected $productCategoryRepository;

    public function __construct(ProductRepository $productRepository, ProductCategoryRepository $productCategoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
    }
    public function deleteProduct($id)
    {
        return $this->productRepository->delete($id);
    }

    public function addCategoriesToProduct($productId, $categoryIds)
    {
        return $this->productCategoryRepository->attachCategories($productId, $categoryIds);
    }

    public function removeCategoryFromProduct($productId, $categoryId)
    {
        return $this->productCategoryRepository->detachCategory($productId, $categoryId);
    }
}