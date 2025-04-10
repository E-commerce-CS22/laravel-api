<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Slide extends Model
{
    use HasFactory;

    protected $fillable = ['order', 'image', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
