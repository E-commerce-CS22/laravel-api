<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishListSeeder extends Seeder
{
    public function run(): void
    {
        // Generate sample wish lists
        $wishlists = [
            ['created_at' => now(), 'updated_at' => now()],
            ['created_at' => now(), 'updated_at' => now()],
            ['created_at' => now(), 'updated_at' => now()],
            ['created_at' => now(), 'updated_at' => now()],
            ['created_at' => now(), 'updated_at' => now()],
        ];

        // Insert into the wish_lists table
        DB::table('wish_lists')->insert($wishlists);
    }
}
