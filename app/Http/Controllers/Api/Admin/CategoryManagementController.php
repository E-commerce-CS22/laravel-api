<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Symfony\Component\HttpFoundation\Response;

class CategoryManagementController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    private function isAdmin(Request $request)
    {
        return $request->user() && $request->user()->admin()->exists();
    }

    public function index(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $categories = $this->categoryService->getAllCategories();
            return CategoryResource::collection($categories);
        } catch (\Exception $e) {
            \Log::error('Error in CategoryManagementController@index: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'message' => 'An error occurred while fetching categories',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $category = $this->categoryService->createCategory($request->all());

            return response()->json([
                'message' => 'Category created successfully',
                'data' => new CategoryResource($category)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            \Log::error('Error in CategoryManagementController@store: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while creating category',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, Category $category)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $this->categoryService->updateCategory($category, $request->all());

            return response()->json([
                'message' => 'Category updated successfully',
                'data' => new CategoryResource($category)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Error in CategoryManagementController@update: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while updating category',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Request $request, Category $category)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $this->categoryService->deleteCategory($category);

            return response()->json([
                'message' => 'Category deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Error in CategoryManagementController@destroy: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while deleting category',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, Category $category)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            return new CategoryResource($category);
        } catch (\Exception $e) {
            \Log::error('Error in CategoryManagementController@show: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching category details',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}