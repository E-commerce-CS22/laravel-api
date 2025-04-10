<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CartProduct extends Pivot
{
    protected $table = 'cart_product'; // Explicitly define the pivot table name

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    /**
     * Define a relationship with the Cart model.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Define a relationship with the Product model.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
