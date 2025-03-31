<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'shipping_address',
        'billing_address',
        'notes',
        'tracking_number',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calculate and update the total amount for the order
     */
    public function calculateTotal()
    {
        $itemsTotal = $this->items->sum('subtotal');
        $this->total_amount = $itemsTotal + $this->shipping_cost + $this->tax_amount;
        return $this->total_amount;
    }

    /**
     * Get all orders with pending status
     */
    public static function pending()
    {
        return self::where('status', 'pending');
    }

    /**
     * Get all orders with processing status
     */
    public static function processing()
    {
        return self::where('status', 'processing');
    }

    /**
     * Get all orders with completed status
     */
    public static function completed()
    {
        return self::where('status', 'completed');
    }

    /**
     * Get all orders with cancelled status
     */
    public static function cancelled()
    {
        return self::where('status', 'cancelled');
    }
}
