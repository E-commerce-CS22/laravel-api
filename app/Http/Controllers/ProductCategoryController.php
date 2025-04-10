<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductCategoryService;

class ProductCategoryController extends Controller
{
    protected $productCategoryService;

    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }

    public function store(Request $request, $productId)
    {
        $categoryIds = $request->input('category_ids');
        return response()->json($this->productCategoryService->addCategoriesToProduct($productId, $categoryIds));
    }

    public function destroy($productId, $categoryId)
    {
        return response()->json($this->productCategoryService->removeCategoryFromProduct($productId, $categoryId));
    }
}
