<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders.
     */
    public function index(Request $request)
    {
        $query = Order::query()->with('items.product', 'items.productVariant', 'user');
        
        // Filter by status if provided
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        
        // Filter by date range
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Search by customer name or email
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $orders = $query->latest()->paginate($request->per_page ?? 15);
        
        return OrderResource::collection($orders);
    }

    /**
     * Display the specified order.
     */
    public function show(string $id)
    {
        $order = Order::with('items.product', 'items.productVariant', 'user')->findOrFail($id);
        
        return new OrderResource($order);
    }

    /**
     * Update the status of an order.
     */
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled,refunded',
        ]);
        
        $order = Order::findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            $oldStatus = $order->status;
            $newStatus = $request->status;
            
            // Handle stock changes if the order is being cancelled or refunded
            if (($oldStatus != 'cancelled' && $oldStatus != 'refunded') && 
                ($newStatus == 'cancelled' || $newStatus == 'refunded')) {
                
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
            }
            
            // Update order status
            $order->status = $newStatus;
            $order->save();
            
            DB::commit();
            
            return new OrderResource($order->load('items.product', 'items.productVariant', 'user'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update payment status of an order.
     */
    public function updatePaymentStatus(Request $request, string $id)
    {
        // $request->validate([
        //     'payment_status' => 'required|string|in:unpaid,paid,failed,refunded',
        // ]);
        
        $order = Order::findOrFail($id);
        $order->save();
        
        return new OrderResource($order->load('items.product', 'items.productVariant', 'user'));
    }

    /**
     * Update tracking information for an order.
     */
    public function updateTracking(Request $request, string $id)
    {
        $request->validate([
            'tracking_number' => 'required|string',
        ]);
        
        $order = Order::findOrFail($id);
        $order->tracking_number = $request->tracking_number;
        
        // Automatically move to processing if it's pending
        if ($order->status === 'pending') {
            $order->status = 'processing';
        }
        
        $order->save();
        
        return new OrderResource($order->load('items.product', 'items.productVariant', 'user'));
    }

    /**
     * Generate order statistics.
     */
    public function statistics(Request $request)
    {
        // Default to last 30 days if no date range is provided
        $fromDate = $request->from_date ?? now()->subDays(30)->toDateString();
        $toDate = $request->to_date ?? now()->toDateString();
        
        $stats = [
            'total_orders' => Order::whereDate('created_at', '>=', $fromDate)
                                 ->whereDate('created_at', '<=', $toDate)
                                 ->count(),
                                 
            'completed_orders' => Order::where('status', 'completed')
                                    ->whereDate('created_at', '>=', $fromDate)
                                    ->whereDate('created_at', '<=', $toDate)
                                    ->count(),
                                    
            'pending_orders' => Order::where('status', 'pending')
                                  ->whereDate('created_at', '>=', $fromDate)
                                  ->whereDate('created_at', '<=', $toDate)
                                  ->count(),
                                  
            'processing_orders' => Order::where('status', 'processing')
                                     ->whereDate('created_at', '>=', $fromDate)
                                     ->whereDate('created_at', '<=', $toDate)
                                     ->count(),
                                     
            'cancelled_orders' => Order::where('status', 'cancelled')
                                    ->whereDate('created_at', '>=', $fromDate)
                                    ->whereDate('created_at', '<=', $toDate)
                                    ->count(),
                                    
            'total_revenue' => Order::whereIn('status', ['completed', 'processing'])
                                  ->whereDate('created_at', '>=', $fromDate)
                                  ->whereDate('created_at', '<=', $toDate)
                                  ->sum('total_amount'),
                                  
            'average_order_value' => Order::whereIn('status', ['completed', 'processing'])
                                       ->whereDate('created_at', '>=', $fromDate)
                                       ->whereDate('created_at', '<=', $toDate)
                                       ->avg('total_amount') ?? 0,
        ];
        
        return response()->json($stats);
    }

    /**
     * Get total orders and monthly breakdown starting from 2024.
     */
    public function getOrderStatistics()
    {
        // Total number of orders
        $totalOrders = DB::table('orders')->count();

        // Monthly breakdown of orders from 2024 to the current date
        $monthlyOrders = DB::table('orders')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', '>=', 2024)
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->get();

        return response()->json([
            'total_orders' => $totalOrders,
            'monthly_orders' => $monthlyOrders
        ]);
    }
}
