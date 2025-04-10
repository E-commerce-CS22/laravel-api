<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('posts')->insert([
            [
                'user_id' => 1,
                'title' => 'أول عنوان للمقالة',
                'body' => 'هذا هو نص المقالة الأولى. يحتوي على نص تجريبي لأغراض الاختبار.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'title' => 'ثاني عنوان للمقالة',
                'body' => 'هذا هو نص المقالة الثانية. يحتوي على المزيد من النص التجريبي للاختبار.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'title' => 'ثالث عنوان للمقالة',
                'body' => 'نص آخر لمقالة للاختبار وإدراج البيانات.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
