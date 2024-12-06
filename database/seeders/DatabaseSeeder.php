<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 30 admins
        for ($i = 1; $i <= 30; $i++) {
            Admin::create([
                'first_name' => "مدير",
                'last_name' => $i . "#",
                'email' => "admin{$i}@example.com",
                'username' => "admin{$i}",
                'phone' => "966500{$i}" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'password' => Hash::make('password123'),
            ]);
        }

        // Create 30 customers with their users
        $saudiCities = [
            'الرياض', 'جدة', 'مكة المكرمة', 'المدينة المنورة', 'الدمام',
            'الخبر', 'الظهران', 'الأحساء', 'الطائف', 'بريدة',
            'تبوك', 'خميس مشيط', 'الجبيل', 'نجران', 'ينبع'
        ];

        for ($i = 1; $i <= 30; $i++) {
            $user = User::create([
                'first_name' => "عميل",
                'last_name' => $i . "#",
                'email' => "customer{$i}@example.com",
                'username' => "customer{$i}",
                'phone' => "966555{$i}" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'password' => Hash::make('password123'),
            ]);

            Customer::create([
                'user_id' => $user->id,
                'address' => "شارع " . rand(1, 50) . "، حي " . rand(1, 20),
                'city' => $saudiCities[array_rand($saudiCities)],
                'postal_code' => rand(11000, 99999),
            ]);
        }

        // Create categories (30)
        $categories = [
            'الإلكترونيات', 'الملابس', 'الأحذية', 'الإكسسوارات', 'المنزل والحديقة',
            'الرياضة', 'الكتب', 'الألعاب', 'مستحضرات التجميل', 'العطور',
            'المجوهرات', 'الساعات', 'الحقائب', 'الأثاث', 'المطبخ',
            'السيارات', 'الصحة', 'الطفل', 'الهدايا', 'الفن والحرف',
            'الموسيقى', 'السفر', 'الحيوانات الأليفة', 'المكتب', 'الحدائق',
            'الأدوات المنزلية', 'الأجهزة', 'الديكور', 'الرياضة المائية', 'التخييم'
        ];

        foreach ($categories as $categoryName) {
            Category::create(['name' => $categoryName]);
        }

        // Create attributes
        $attributes = [
            'المقاس' => ['صغير جداً', 'صغير', 'متوسط', 'كبير', 'كبير جداً', 'مقاس موحد'],
            'اللون' => ['أحمر', 'أزرق', 'أخضر', 'أسود', 'أبيض', 'ذهبي', 'فضي', 'بني', 'برتقالي', 'أصفر'],
            'المادة' => ['قطن', 'بوليستر', 'جلد', 'حرير', 'صوف', 'كتان', 'جينز', 'ساتان', 'شيفون', 'دانتيل'],
            'النوع' => ['رجالي', 'نسائي', 'أطفال', 'للجنسين'],
            'الموسم' => ['صيف', 'شتاء', 'ربيع', 'خريف', 'جميع المواسم']
        ];

        foreach ($attributes as $attributeName => $values) {
            $attribute = Attribute::create(['name' => $attributeName]);
            foreach ($values as $value) {
                AttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'name' => $value
                ]);
            }
        }

        // Arabic product names and descriptions
        $products = [];
        $productTypes = [
            'قميص' => 'قميص كلاسيكي بتصميم أنيق',
            'بنطلون' => 'بنطلون عصري مريح',
            'فستان' => 'فستان أنيق للمناسبات',
            'حذاء' => 'حذاء رياضي خفيف',
            'جاكيت' => 'جاكيت شتوي دافئ',
            'تنورة' => 'تنورة عصرية أنيقة',
            'معطف' => 'معطف شتوي فاخر',
            'بلوزة' => 'بلوزة نسائية أنيقة',
            'جينز' => 'جينز عصري مريح',
            'تي شيرت' => 'تي شيرت قطني مريح'
        ];

        foreach ($productTypes as $type => $baseDesc) {
            for ($i = 1; $i <= 3; $i++) {
                $products[] = [
                    'name' => $type . ' ' . $i,
                    'description' => $baseDesc . ' - موديل ' . $i,
                    'price' => rand(50, 500),
                    'category' => $categories[array_rand($categories)]
                ];
            }
        }

        // Create 30 products with variants
        foreach ($products as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price']
            ]);

            // Attach random categories (1-3 categories per product)
            $categoryCount = rand(1, 3);
            $randomCategories = Category::inRandomOrder()->limit($categoryCount)->get();
            foreach ($randomCategories as $category) {
                $product->categories()->attach($category->id);
            }

            // Create variants
            $sizes = AttributeValue::whereHas('attribute', function($q) {
                $q->where('name', 'المقاس');
            })->get();
            
            $colors = AttributeValue::whereHas('attribute', function($q) {
                $q->where('name', 'اللون');
            })->get();

            foreach ($sizes as $size) {
                foreach ($colors as $color) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => 'PRD-' . $product->id . '-' . $size->id . '-' . $color->id . '-' . rand(100, 999),
                        'extra_price' => rand(0, 50),
                        'stock' => rand(10, 100)
                    ]);
                    
                    // Attach attribute values using the pivot table
                    DB::table('attribute_product_variant')->insert([
                        [
                            'attribute_value_id' => $size->id,
                            'product_variant_id' => $variant->id,
                            'attribute_id' => $size->attribute_id
                        ],
                        [
                            'attribute_value_id' => $color->id,
                            'product_variant_id' => $variant->id,
                            'attribute_id' => $color->attribute_id
                        ]
                    ]);
                }
            }
        }
    }
}
