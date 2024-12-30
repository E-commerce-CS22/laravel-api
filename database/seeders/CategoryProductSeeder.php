<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch related data
        $products = Product::all();
        $categories = Category::all();

        // Ensure related tables have data
        if ($products->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Related tables (products, categories) must have data before seeding category_product.');
            return;
        }

        // Seed the category_product table
        foreach ($products as $product) {
            // Assign 1-3 random categories to each product
            $assignedCategories = $categories->random(rand(1, min(3, $categories->count())));

            foreach ($assignedCategories as $category) {
                DB::table('category_product')->insert([
                    'product_id' => $product->id,
                    'category_id' => $category->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
