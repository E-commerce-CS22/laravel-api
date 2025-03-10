<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CartSeeder::class,
            CustomerSeeder::class,
            TagSeeder::class,
            ProductSeeder::class,
            CartProductSeeder::class,
            CategorySeeder::class,
            CategoryProductSeeder::class,
            TagSeeder::class,
            AttributeSeeder::class,
            AttributeValueSeeder::class,
            ProductVariantSeeder::class,
            AttributeProductVariantSeeder::class,
            WishlistSeeder::class,
            ProductWishlistSeeder::class,

        ]);
    }
}
