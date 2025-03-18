<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'user_id',
        'address',
        'city',
        'postal_code',
        'country',
        'profile',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function wishList()
    {
        return $this->belongsTo(WishList::class, 'wishlist_id');
    }
}
