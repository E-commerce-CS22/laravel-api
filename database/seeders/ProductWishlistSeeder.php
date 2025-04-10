<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Wishlist;

class ProductWishlistSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch related data
        $products = Product::all();
        $wishlists = Wishlist::all();

        // Ensure related tables have data
        if ($products->isEmpty() || $wishlists->isEmpty()) {
            $this->command->warn('Related tables (products, wish_lists) must have data before seeding product_wishlist.');
            return;
        }

        // Seed the product_wishlist table
        foreach ($wishlists as $wishlist) {
            // Assign 1-5 random products to each wishlist
            $assignedProducts = $products->random(rand(1, min(5, $products->count())));

            foreach ($assignedProducts as $product) {
                DB::table('product_wishlist')->insert([
                    'product_id' => $product->id,
                    'wishlist_id' => $wishlist->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
