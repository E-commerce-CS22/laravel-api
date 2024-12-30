<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'discount_type',
        'discount_value',
        'status',
        'discount_start_date',
        'discount_end_date',
    ];
    public function wishlists()
    {
        return $this->belongsToMany(WishList::class, 'product_wishlist', 'product_id', 'wishlist_id');
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_product', 'product_id', 'cart_id')->withPivot('quantity')->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
