<?php

namespace App\Repositories;

use App\Models\ProductImage;

class ProductImageRepository
{
    protected $model;

    public function __construct(ProductImage $model)
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
        $record = $this->findById($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record = $this->findById($id);
        $record->delete();
        return $record;
    }
}
