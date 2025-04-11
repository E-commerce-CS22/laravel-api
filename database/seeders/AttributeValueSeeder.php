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
            ['attribute_id' => 1, 'name' => 'أحمر', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'أزرق', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'أخضر', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'أصفر', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'أسود', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'أبيض', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'فضي', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'ذهبي', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'رمادي', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 1, 'name' => 'بنفسجي', 'created_at' => now(), 'updated_at' => now()],

            // Sizes
            ['attribute_id' => 2, 'name' => 'صغير', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 2, 'name' => 'متوسط', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 2, 'name' => 'كبير', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 2, 'name' => 'كبير جدا', 'created_at' => now(), 'updated_at' => now()],

            // Materials
            ['attribute_id' => 3, 'name' => 'قطن', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'بلاستيك', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'خشب', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'زجاج', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'معدن', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'ألومنيوم', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'جلد', 'created_at' => now(), 'updated_at' => now()],

            // Storage
            ['attribute_id' => 4, 'name' => '32GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 4, 'name' => '64GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 4, 'name' => '128GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 4, 'name' => '256GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 4, 'name' => '512GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 4, 'name' => '1TB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 4, 'name' => '2TB', 'created_at' => now(), 'updated_at' => now()],

            // RAM
            ['attribute_id' => 5, 'name' => '2GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 5, 'name' => '4GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 5, 'name' => '6GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 5, 'name' => '8GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 5, 'name' => '12GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 5, 'name' => '16GB', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 5, 'name' => '32GB', 'created_at' => now(), 'updated_at' => now()],

            // Processor
            ['attribute_id' => 6, 'name' => 'Snapdragon 8 Gen 2', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => 'Apple A16 Bionic', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => 'Intel Core i5', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => 'Intel Core i7', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => 'Intel Core i9', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => 'AMD Ryzen 5', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => 'AMD Ryzen 7', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => 'AMD Ryzen 9', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 6, 'name' => 'MediaTek Dimensity 9000', 'created_at' => now(), 'updated_at' => now()],

            // Screen Size
            ['attribute_id' => 7, 'name' => '5.5 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 7, 'name' => '6.1 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 7, 'name' => '6.7 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 7, 'name' => '13.3 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 7, 'name' => '14 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 7, 'name' => '15.6 inch', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 7, 'name' => '16 inch', 'created_at' => now(), 'updated_at' => now()],

            // Battery Capacity
            ['attribute_id' => 8, 'name' => '3000mAh', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => '4000mAh', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => '5000mAh', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => '20 hours', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 8, 'name' => '30 hours', 'created_at' => now(), 'updated_at' => now()],

            // Graphics Card
            ['attribute_id' => 9, 'name' => 'NVIDIA RTX 3050', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 9, 'name' => 'NVIDIA RTX 3060', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 9, 'name' => 'NVIDIA RTX 4060', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 9, 'name' => 'AMD Radeon RX 6600', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 9, 'name' => 'Intel Iris Xe', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 9, 'name' => 'Apple M2', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}