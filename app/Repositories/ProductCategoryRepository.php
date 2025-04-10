<?php

namespace App\Repositories;

use App\Models\Product;

class ProductCategoryRepository
{
    public function attachCategories($productId, $categoryIds)
    {
        $product = Product::findOrFail($productId);
        $existingCategoryIds = $product->categories()->pluck('categories.id')->toArray(); // Specify table name for 'id'
        $uniqueCategoryIds = array_diff($categoryIds, $existingCategoryIds); // Filter out already attached categories
        return $product->categories()->attach($uniqueCategoryIds);
    }
    public function detachCategory($productId, $categoryId)
    {
        $product = Product::findOrFail($productId);
        return $product->categories()->detach($categoryId);
    }
}
