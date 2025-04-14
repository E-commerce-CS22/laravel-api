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
        'parent_id',
        'is_parent',
        'discount_type',
        'discount_value',
        'status',
        'discount_start_date',
        'discount_end_date',
        'images',
        'attributes',
        'sku',
        'stock',
        'is_default',
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

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function getImageUrlsAttribute()
    {
        return $this->images->map(function ($image) {
            return [
                'url' => asset('storage/products/' . $image->image),
                'is_primary' => $image->is_primary,
                'alt_text' => $image->alt_text,
                'sort_order' => $image->sort_order,
            ];
        });
    }
}
