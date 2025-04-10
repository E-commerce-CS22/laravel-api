<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_id',
        'name'
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'attribute_product_variant')
            ->withPivot('attribute_id')
            ->withTimestamps();
    }
}