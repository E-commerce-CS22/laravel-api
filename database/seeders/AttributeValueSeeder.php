<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attribute_values')->insert([
            // Colors
            ['attribute_id' => 1, 'name' => 'Red', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'Blue', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'Green', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'Yellow', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'Black', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'White', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'Silver', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'Gold', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'Gray', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'Purple', 'created_at' => now(), 'updated_at' => now()],

            // Sizes
            ['attribute_id' => 2, 'name' => 'Small', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 2, 'name' => 'Medium', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 2, 'name' => 'Large', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 2, 'name' => 'XL', 'created_at' => now(), 'updated_at' => now()],

            // Materials
            ['attribute_id' => 3, 'name' => 'Cotton', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'Plastic', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'Wood', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'Glass', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'Metal', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'Aluminum', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'Leather', 'created_at' => now(), 'updated_at' => now()],

            // Storage
            ['attribute_id' => 6, 'name' => '64GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => '128GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => '256GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => '512GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => '1TB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => '2TB', 'created_at' => now(), 'updated_at' => now()],

            // RAM
            ['attribute_id' => 7, 'name' => '4GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 7, 'name' => '6GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 7, 'name' => '8GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 7, 'name' => '12GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 7, 'name' => '16GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 7, 'name' => '32GB', 'created_at' => now(), 'updated_at' => now()],

            // Processor
            ['attribute_id' => 8, 'name' => 'Snapdragon 8 Gen 2', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => 'Apple A16 Bionic', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => 'Intel Core i5', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => 'Intel Core i7', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => 'Intel Core i9', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => 'AMD Ryzen 5', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => 'AMD Ryzen 7', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => 'AMD Ryzen 9', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => 'MediaTek Dimensity 9000', 'created_at' => now(), 'updated_at' => now()],

            // Screen Size
            ['attribute_id' => 9, 'name' => '5.5 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 9, 'name' => '6.1 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 9, 'name' => '6.7 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 9, 'name' => '13.3 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 9, 'name' => '14 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 9, 'name' => '15.6 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 9, 'name' => '16 inch', 'created_at' => now(), 'updated_at' => now()],

            // Connectivity
            ['attribute_id' => 10, 'name' => 'Bluetooth 5.0', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 10, 'name' => 'Bluetooth 5.2', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 10, 'name' => 'Bluetooth 5.3', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 10, 'name' => 'Wired', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 10, 'name' => 'Wi-Fi 6', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 10, 'name' => 'Wi-Fi 6E', 'created_at' => now(), 'updated_at' => now()],

            // Battery Capacity
            ['attribute_id' => 11, 'name' => '3000mAh', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 11, 'name' => '4000mAh', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 11, 'name' => '5000mAh', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 11, 'name' => '20 hours', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 11, 'name' => '30 hours', 'created_at' => now(), 'updated_at' => now()],

            // Graphics Card
            ['attribute_id' => 12, 'name' => 'NVIDIA RTX 3050', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 12, 'name' => 'NVIDIA RTX 3060', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 12, 'name' => 'NVIDIA RTX 4060', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 12, 'name' => 'AMD Radeon RX 6600', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 12, 'name' => 'Intel Iris Xe', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 12, 'name' => 'Apple M2', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}