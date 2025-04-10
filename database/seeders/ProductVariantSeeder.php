<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_variants')->insert([
            [
                'product_id' => 1,
                'sku' => 'VARIANT-001',
                'stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'sku' => 'VARIANT-002',
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'sku' => 'VARIANT-003',
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'sku' => 'VARIANT-004',
                'stock' => 75,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
