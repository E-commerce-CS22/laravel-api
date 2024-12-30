<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attributes')->insert([
            ['id' => 1, 'name' => 'اللون', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'الحجم', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'المادة', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'الشكل', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'التصميم', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
