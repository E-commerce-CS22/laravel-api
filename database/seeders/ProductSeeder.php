<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'قميص قطني كلاسيكي',
                'description' => 'قميص قطني عالي الجودة متوفر بعدة ألوان ومقاسات',
                'price' => 99.99,
            ],
            [
                'name' => 'بنطلون جينز عصري',
                'description' => 'بنطلون جينز عصري مريح ومتين',
                'price' => 149.99,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
