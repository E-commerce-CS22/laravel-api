<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CartSeeder::class,
            CustomerSeeder::class,
            AttributeSeeder::class,
            AttributeValueSeeder::class,
            TagSeeder::class,
            ProductSeeder::class,
            ImageSeeder::class,
            CartProductSeeder::class,
            CategorySeeder::class,
            CategoryProductSeeder::class,
            TagSeeder::class,
            ProductVariantSeeder::class,
            AttributeProductVariantSeeder::class,
            WishlistSeeder::class,
            ProductWishlistSeeder::class,

        ]);
    }
}
