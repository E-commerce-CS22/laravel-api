<?php

namespace App\Repositories;

use App\Models\ProductVariant;

class ProductVariantRepository
{
    protected $model;

    public function __construct(ProductVariant $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findWhere(array $criteria)
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $variant = $this->findById($id);
        $variant->update($data);
        return $variant;
    }

    public function delete($id)
    {
        $variant = $this->findById($id);
        $variant->delete();
        return $variant;
    }
    
    /**
     * Update all records that don't match the given criteria
     * 
     * @param array $criteria Criteria to exclude from update
     * @param array $data Data to update
     * @return int Number of records updated
     */
    public function updateWhereNot(array $criteria, array $data)
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $key => $value) {
            if (is_array($value)) {
                $query->whereNotIn($key, $value);
            } else {
                $query->where($key, '!=', $value);
            }
        }
        
        return $query->update($data);
    }
}
