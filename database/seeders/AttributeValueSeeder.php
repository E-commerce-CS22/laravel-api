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

            // Sizes
            ['attribute_id' => 2, 'name' => 'صغير', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 2, 'name' => 'متوسط', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 2, 'name' => 'كبير', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 2, 'name' => 'ضخم', 'created_at' => now(), 'updated_at' => now()],

            // Materials
            ['attribute_id' => 3, 'name' => 'قطن', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'بلاستيك', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'خشب', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'زجاج', 'created_at' => now(), 'updated_at' => now()],
            ['attribute_id' => 3, 'name' => 'معدن', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
