<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getAll($perPage = null, $page = null)
    {
        $query = Product::query();
        
        if ($perPage !== null) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }
        
        return $query->get();
    }

    public function create($data)
    {
        // Filter out fields that don't exist in the database schema
        $filteredData = collect($data)->only([
            'name',
            'description',
            'price',
            'parent_id',
            'is_parent',
            'discount_type',
            'discount_value',
            'status',
            'discount_start_date',
            'discount_end_date',
            'images',
            'attributes',
            'is_default',
        ])->toArray();
        
        return Product::create($filteredData);
    }

    public function findById($id)
    {
        return Product::findOrFail($id);
    }
    
    public function findWhere(array $criteria, $perPage = null, $page = null)
    {
        $query = Product::query();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        if ($perPage !== null) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }
        
        return $query->get();
    }

    public function update($id, $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return $product;
    }
}
