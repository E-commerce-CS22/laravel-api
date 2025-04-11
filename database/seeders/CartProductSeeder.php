<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Product;

class CartProductSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch related data
        $carts = Cart::all();
        $products = Product::all();

        // Ensure related tables have data
        if ($carts->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Related tables (carts, products) must have data before seeding cart_product.');
            return;
        }

        // Seed the cart_product table
        foreach ($carts as $cart) {
            // Randomly assign products to each cart
            $numberOfProducts = rand(1, min(5, $products->count())); // Adjust based on available products
            $assignedProducts = $products->random($numberOfProducts);

            foreach ($assignedProducts as $product) {
                DB::table('cart_product')->insert([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 10), // Random quantity between 1 and 10
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}