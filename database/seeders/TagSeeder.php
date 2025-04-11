<?php
namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'ملابس',
            'هاتف ذكي',
            '',
        ];

        foreach ($tags as $tagName) {
            // Use firstOrCreate to avoid inserting duplicates
            Tag::firstOrCreate([
                'name' => $tagName
            ], [
                'slug' => str_replace(' ', '-', $tagName),
                'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)), // Generate random color
            ]);
        }
    }
}
