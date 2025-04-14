<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_images')->insert([
            [
                'product_id' => 1,
                'product_variant_id' => null,
                'image' => '1.jpg',
                'alt_text' => 'Image 1',
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'product_variant_id' => null,
                'image' => '2.webp',
                'alt_text' => 'Image 2',
                'is_primary' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'product_variant_id' => null,
                'image' => '3.webp',
                'alt_text' => 'Image 3',
                'is_primary' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'product_variant_id' => null,
                'image' => '4.webp',
                'alt_text' => 'Image 4',
                'is_primary' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'product_variant_id' => null,
                'image' => '5.webp',
                'alt_text' => 'Image 5',
                'is_primary' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
