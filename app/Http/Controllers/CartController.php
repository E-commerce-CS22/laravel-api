<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Display the cart
    public function index()
    {
        $cart = Cart::with('products')->where('user_id', auth()->id())->first();
        return response()->json($cart);
    }

    // Add product to cart
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $cart->products()->syncWithoutDetaching([
            $request->product_id => ['quantity' => $request->quantity],
        ]);

        return response()->json(['message' => 'Product added to cart']);
    }

    // Update product quantity in the cart
    public function update(Request $request, $productId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = Cart::where('user_id', auth()->id())->firstOrFail();
        $cart->products()->updateExistingPivot($productId, ['quantity' => $request->quantity]);

        return response()->json(['message' => 'Cart updated']);
    }

    // Remove a product from the cart
    public function remove($productId)
    {
        $cart = Cart::where('user_id', auth()->id())->firstOrFail();
        $cart->products()->detach($productId);

        return response()->json(['message' => 'Product removed from cart']);
    }

    // Clear the cart
    public function clear()
    {
        $cart = Cart::where('user_id', auth()->id())->firstOrFail();
        $cart->products()->detach();

        return response()->json(['message' => 'Cart cleared']);
    }
}
