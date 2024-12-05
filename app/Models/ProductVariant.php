<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_product_variant')
            ->withPivot('attribute_value_id')
            ->withTimestamps();
    }

}
