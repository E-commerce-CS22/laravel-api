<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {

        $categories = [
            [
                'name' => 'هواتف',
                'description' => 'كل ما يتعلق بهواتف الهواتف الذكية',
                'image' => 'categories/phones.png'
            ],
            [
                'name' => 'الرجال',
                'description' => 'كل ما يتعلق بالرجال',
                'image' => 'categories/men.png'
            ],
            [
                'name' => 'النساء',
                'description' => 'كل ما يتعلق بالنساء',
                'image' => 'categories/women.png'
            ],
            [
                'name' => 'المنزل',
                'description' => 'كل ما يتعلق بالمنزل',
                'image' => 'categories/houseware.jpg'
            ],
            [
                'name' => 'العطور',
                'description' => 'كل ما يتعلق بالعطور',
                'image' => 'categories/perfume.jpg'
            ]
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description'],
                'slug' => str_replace(' ', '-', $category['name']),
                'image' => $category['image']
            ]);
        }
    }
}
