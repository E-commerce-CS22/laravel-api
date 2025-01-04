<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getAll()
    {
        return Product::all();
    }

    public function create($data)
    {
        return Product::create($data);
    }

    public function findById($id)
    {
        return Product::findOrFail($id);
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
