<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 
        'sku', 
        'price',
        'extra_price', 
        'stock',
        'is_default',
        'variant_title',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
        'is_default' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_product_variant')
            ->withPivot('attribute_value_id')
            ->withTimestamps();
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_product_variant')
            ->withPivot('attribute_id')
            ->withTimestamps();
    }
}
