<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['id'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_product', 'cart_id', 'product_id')->withPivot('quantity')->withTimestamps();
    }
}
