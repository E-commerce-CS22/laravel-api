<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_wishlist', 'wishlist_id', 'product_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'cart_id');
    }
}