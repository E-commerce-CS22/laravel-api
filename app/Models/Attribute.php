<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'name'
    ];

    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'attribute_product_variant')
            ->withPivot('attribute_value_id')
            ->withTimestamps();
    }

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

}
