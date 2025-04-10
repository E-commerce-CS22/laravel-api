<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
    for ($i = 1; $i <= 30; $i++) {
        DB::table('carts')->insert([
            'id' => $i,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    }
}
