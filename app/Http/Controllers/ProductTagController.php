<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductTagService;

class ProductTagController extends Controller
{
    protected $productTagService;

    public function __construct(ProductTagService $productTagService)
    {
        $this->productTagService = $productTagService;
    }

    public function store(Request $request, $productId)
    {
        $tagIds = $request->input('tag_ids');
        return response()->json($this->productTagService->addTagsToProduct($productId, $tagIds));
    }

    public function destroy($productId, $tagId)
    {
        return response()->json($this->productTagService->removeTagFromProduct($productId, $tagId));
    }
}
