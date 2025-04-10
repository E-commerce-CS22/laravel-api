<?php

namespace App\Services;

use App\Repositories\ProductTagRepository;

class ProductTagService
{
    protected $productTagRepository;

    public function __construct(ProductTagRepository $productTagRepository)
    {
        $this->productTagRepository = $productTagRepository;
    }

    public function addTagsToProduct($productId, $tagIds)
    {
        return $this->productTagRepository->attachTags($productId, $tagIds);
    }

    public function removeTagFromProduct($productId, $tagId)
    {
        return $this->productTagRepository->detachTag($productId, $tagId);
    }
}
