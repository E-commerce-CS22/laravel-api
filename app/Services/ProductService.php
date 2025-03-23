<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts()
    {
        return $this->productRepository->getAll();
    }

    public function createProduct($data)
    {
        return $this->productRepository->create($data);
    }

    public function getProductById($id)
    {
        return $this->productRepository->findById($id);
    }

    public function updateProduct($id, $data)
    {
        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct($id)
    {
        return $this->productRepository->delete($id);
    }
    
    /**
     * Apply discount to a product
     * 
     * @param int $id
     * @param array $discountData
     * @return \App\Models\Product
     */
    public function applyDiscount($id, array $discountData)
    {
        return $this->productRepository->update($id, $discountData);
    }
    
    /**
     * Remove discount from a product
     * 
     * @param int $id
     * @return \App\Models\Product
     */
    public function removeDiscount($id)
    {
        $discountData = [
            'discount_type' => null,
            'discount_value' => null,
            'discount_start_date' => null,
            'discount_end_date' => null
        ];
        
        return $this->productRepository->update($id, $discountData);
    }
}
