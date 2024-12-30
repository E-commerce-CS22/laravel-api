<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->getAllCategories();
    }

    public function createCategory(array $data)
    {
        return $this->categoryRepository->createCategory($data);
    }

    public function updateCategory(Category $category, array $data)
    {
        return $this->categoryRepository->updateCategory($category, $data);
    }

    public function deleteCategory(Category $category)
    {
        return $this->categoryRepository->deleteCategory($category);
    }
}
