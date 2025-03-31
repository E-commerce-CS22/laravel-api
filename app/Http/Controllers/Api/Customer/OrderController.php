<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the customer's orders.
     */
    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('items.product', 'items.productVariant')->latest()->get();

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            // Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address ?: $request->shipping_address,
                'payment_method' => $request->payment_method,
                'shipping_method' => $request->shipping_method,
                'shipping_cost' => 0, // You can calculate this based on shipping method
                'tax_amount' => 0, // You can calculate this based on your tax rules
                'notes' => $request->notes,
            ]);

            // Create the order items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $variant = null;
                $price = $product->price;
                
                // If product variant exists, use its price
                if (!empty($item['product_variant_id'])) {
                    $variant = ProductVariant::findOrFail($item['product_variant_id']);
                    $price = $variant->price;
                    
                    // Check stock availability
                    if ($variant->stock < $item['quantity']) {
                        throw new \Exception("Not enough stock available for {$product->name} variant.");
                    }
                    
                    // Update stock
                    $variant->stock -= $item['quantity'];
                    $variant->save();
                } else {
                    // Check stock availability for product without variants
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Not enough stock available for {$product->name}.");
                    }
                    
                    // Update stock
                    $product->stock -= $item['quantity'];
                    $product->save();
                }
                
                // Apply discount if product has active discount
                $discountAmount = 0;
                if ($product->discount && now()->between($product->discount->start_date, $product->discount->end_date)) {
                    if ($product->discount->type === 'percentage') {
                        $discountAmount = ($price * $product->discount->value / 100) * $item['quantity'];
                    } else {
                        $discountAmount = $product->discount->value * $item['quantity'];
                    }
                }
                
                // Create order item
                $orderItem = new OrderItem([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $price,
                    'discount_amount' => $discountAmount,
                ]);
                
                $orderItem->calculateSubtotal();
                $order->items()->save($orderItem);
            }
            
            // Calculate and update order total
            $order->calculateTotal();
            $order->save();
            
            DB::commit();
            
            return (new OrderResource($order->load('items.product', 'items.productVariant')))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $order = $user->orders()->with('items.product', 'items.productVariant')->findOrFail($id);
        
        return new OrderResource($order);
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(string $id)
    {
        $user = Auth::user();
        $order = $user->orders()->findOrFail($id);
        
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'message' => 'Only pending or processing orders can be cancelled.'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            DB::beginTransaction();
            
            // Restore stock for each order item
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    $variant = $item->productVariant;
                    $variant->stock += $item->quantity;
                    $variant->save();
                } else {
                    $product = $item->product;
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }
            
            $order->status = 'cancelled';
            $order->save();
            
            DB::commit();
            
            return new OrderResource($order->load('items.product', 'items.productVariant'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
