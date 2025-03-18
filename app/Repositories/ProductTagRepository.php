<?php

namespace App\Repositories;

use App\Models\Product;

class ProductTagRepository
{
    public function attachTags($productId, $tagIds)
    {
        $product = Product::findOrFail($productId);
        $existingTagIds = $product->tags()->pluck('tags.id')->toArray(); // Specify table name for 'id'
        $uniqueTagIds = array_diff($tagIds, $existingTagIds); // Filter out already attached tags
        return $product->tags()->attach($uniqueTagIds);
    }

    public function detachTag($productId, $tagId)
    {
        $product = Product::findOrFail($productId);
        return $product->tags()->detach($tagId);
    }
}
