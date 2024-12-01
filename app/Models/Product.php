<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function wishlists()
{
    return $this->belongsToMany(WishList::class, 'product_wishlist', 'product_id', 'wishlist_id');
}
}
