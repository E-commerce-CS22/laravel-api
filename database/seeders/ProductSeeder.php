<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create attributes if they don't exist
        $this->createAttributes();
        
        // Smartphones
        $this->createSamsungS24();
        $this->createIPhone15Pro();
        $this->createGooglePixel8Pro();
        $this->createXiaomiRedmiNote12();
        
        // Laptops
        $this->createMacbookPro();
        $this->createDellXPS15();
        $this->createLenovoThinkpad();
        
        // Headphones
        $this->createSonyWH1000XM5();
        $this->createAirPodsPro();
        $this->createBoseSoundSport();
    }
    
    private function createAttributes()
    {
        // Check if attributes exist, if not create them
        if (Attribute::count() == 0) {
            $attributeSeeder = new AttributeSeeder();
            $attributeSeeder->run();
        }
    }

    private function createSamsungS24()
    {
        // Create parent product
        $parent = Product::create([
            'name' => 'سامسونج جالاكسي إس 24 ألترا',
            'description' => 'هاتف ذكي متطور مع كاميرا احترافية وشاشة عالية الدقة وأداء قوي',
            'price' => 1399.99,
            'is_parent' => true,
            'parent_id' => null,
        ]);

        // Add product images
        ProductImage::create([
            'product_id' => $parent->id,
            'product_variant_id' => null,
            'image_path' => 'samsung-s24-ultra-main.jpg',
            'alt_text' => 'سامسونج جالاكسي إس 24 ألترا',
            'is_primary' => true,
            'image_type' => 'main'
        ]);

        // Create variants
        $variants = [
            [
                'storage' => '256 جيجابايت',
                'ram' => '12 جيجابايت',
                'color' => 'تيتانيوم أسود',
                'price' => 1399.99,
                'sku' => 'SGS24U-256-12-TB',
                'stock' => 50,
                'is_default' => true,
            ],
            [
                'storage' => '256 جيجابايت',
                'ram' => '12 جيجابايت',
                'color' => 'تيتانيوم رمادي',
                'price' => 1399.99,
                'sku' => 'SGS24U-256-12-TG',
                'stock' => 45,
                'is_default' => false,
            ],
            [
                'storage' => '256 جيجابايت',
                'ram' => '12 جيجابايت',
                'color' => 'تيتانيوم بنفسجي',
                'price' => 1399.99,
                'sku' => 'SGS24U-256-12-TV',
                'stock' => 40,
                'is_default' => false,
            ],
            [
                'storage' => '512 جيجابايت',
                'ram' => '12 جيجابايت',
                'color' => 'تيتانيوم أسود',
                'price' => 1599.99,
                'sku' => 'SGS24U-512-12-TB',
                'stock' => 35,
                'is_default' => false,
            ],
            [
                'storage' => '512 جيجابايت',
                'ram' => '12 جيجابايت',
                'color' => 'تيتانيوم رمادي',
                'price' => 1599.99,
                'sku' => 'SGS24U-512-12-TG',
                'stock' => 30,
                'is_default' => false,
            ],
            [
                'storage' => '1 تيرابايت',
                'ram' => '12 جيجابايت',
                'color' => 'تيتانيوم أسود',
                'price' => 1799.99,
                'sku' => 'SGS24U-1TB-12-TB',
                'stock' => 25,
                'is_default' => false,
            ],
        ];

        // Get attribute IDs
        $storageAttribute = Attribute::where('name', 'التخزين')->first();
        $ramAttribute = Attribute::where('name', 'الذاكرة')->first();
        $colorAttribute = Attribute::where('name', 'اللون')->first();

        foreach ($variants as $variant) {
            // Create variant title
            $variantTitle = "سامسونج جالاكسي إس 24 ألترا - {$variant['storage']} - {$variant['ram']} - {$variant['color']}";
            
            // Create product variant
            $productVariant = ProductVariant::create([
                'product_id' => $parent->id,
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'is_default' => $variant['is_default'],
                'variant_title' => $variantTitle,
            ]);
            
            // Add variant images
            ProductImage::create([
                'product_id' => null,
                'product_variant_id' => $productVariant->id,
                'image_path' => "samsung-s24-ultra-{$variant['color']}.jpg",
                'alt_text' => $variantTitle,
                'is_primary' => true,
                'image_type' => 'main'
            ]);
            
            // Assign attributes to variant
            // Storage attribute
            $storageValue = AttributeValue::where('attribute_id', $storageAttribute->id)
                ->where('name', $variant['storage'])
                ->first();
                
            if ($storageValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $storageAttribute->id,
                    'attribute_value_id' => $storageValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // RAM attribute
            $ramValue = AttributeValue::where('attribute_id', $ramAttribute->id)
                ->where('name', $variant['ram'])
                ->first();
                
            if ($ramValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $ramAttribute->id,
                    'attribute_value_id' => $ramValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Color attribute
            $colorValue = AttributeValue::where('attribute_id', $colorAttribute->id)
                ->where('name', $variant['color'])
                ->first();
                
            if ($colorValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $colorAttribute->id,
                    'attribute_value_id' => $colorValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createIPhone15Pro()
    {
        // Create parent product
        $parent = Product::create([
            'name' => 'آيفون 15 برو',
            'description' => 'هاتف آبل الرائد مع معالج A17 Pro وكاميرا متطورة وتصميم من التيتانيوم',
            'price' => 999.99,
            'is_parent' => true,
            'parent_id' => null,
        ]);

        // Add product images
        ProductImage::create([
            'product_id' => $parent->id,
            'product_variant_id' => null,
            'image_path' => 'iphone15-pro-main.jpg',
            'alt_text' => 'آيفون 15 برو',
            'is_primary' => true,
            'image_type' => 'main'
        ]);

        // Create variants
        $variants = [
            [
                'storage' => '128 جيجابايت',
                'color' => 'تيتانيوم طبيعي',
                'price' => 999.99,
                'sku' => 'IP15P-128-NT',
                'stock' => 100,
                'is_default' => true,
            ],
            [
                'storage' => '256 جيجابايت',
                'color' => 'تيتانيوم طبيعي',
                'price' => 1099.99,
                'sku' => 'IP15P-256-NT',
                'stock' => 85,
                'is_default' => false,
            ],
            [
                'storage' => '512 جيجابايت',
                'color' => 'تيتانيوم طبيعي',
                'price' => 1299.99,
                'sku' => 'IP15P-512-NT',
                'stock' => 60,
                'is_default' => false,
            ],
            [
                'storage' => '1 تيرابايت',
                'color' => 'تيتانيوم طبيعي',
                'price' => 1499.99,
                'sku' => 'IP15P-1TB-NT',
                'stock' => 40,
                'is_default' => false,
            ],
            [
                'storage' => '128 جيجابايت',
                'color' => 'تيتانيوم أزرق',
                'price' => 999.99,
                'sku' => 'IP15P-128-BT',
                'stock' => 90,
                'is_default' => false,
            ],
            [
                'storage' => '256 جيجابايت',
                'color' => 'تيتانيوم أزرق',
                'price' => 1099.99,
                'sku' => 'IP15P-256-BT',
                'stock' => 75,
                'is_default' => false,
            ],
            [
                'storage' => '128 جيجابايت',
                'color' => 'تيتانيوم أبيض',
                'price' => 999.99,
                'sku' => 'IP15P-128-WT',
                'stock' => 80,
                'is_default' => false,
            ],
            [
                'storage' => '256 جيجابايت',
                'color' => 'تيتانيوم أبيض',
                'price' => 1099.99,
                'sku' => 'IP15P-256-WT',
                'stock' => 65,
                'is_default' => false,
            ],
            [
                'storage' => '128 جيجابايت',
                'color' => 'تيتانيوم أسود',
                'price' => 999.99,
                'sku' => 'IP15P-128-BKT',
                'stock' => 95,
                'is_default' => false,
            ],
            [
                'storage' => '256 جيجابايت',
                'color' => 'تيتانيوم أسود',
                'price' => 1099.99,
                'sku' => 'IP15P-256-BKT',
                'stock' => 80,
                'is_default' => false,
            ],
        ];

        // Get attribute IDs
        $storageAttribute = Attribute::where('name', 'التخزين')->first();
        $colorAttribute = Attribute::where('name', 'اللون')->first();

        foreach ($variants as $variant) {
            // Create variant title
            $variantTitle = "آيفون 15 برو - {$variant['storage']} - {$variant['color']}";
            
            // Create product variant
            $productVariant = ProductVariant::create([
                'product_id' => $parent->id,
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'is_default' => $variant['is_default'],
                'variant_title' => $variantTitle,
            ]);
            
            // Add variant images
            ProductImage::create([
                'product_id' => null,
                'product_variant_id' => $productVariant->id,
                'image_path' => "iphone15-pro-{$variant['color']}.jpg",
                'alt_text' => $variantTitle,
                'is_primary' => true,
                'image_type' => 'main'
            ]);
            
            // Assign attributes to variant
            // Storage attribute
            $storageValue = AttributeValue::where('attribute_id', $storageAttribute->id)
                ->where('name', $variant['storage'])
                ->first();
                
            if ($storageValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $storageAttribute->id,
                    'attribute_value_id' => $storageValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Color attribute
            $colorValue = AttributeValue::where('attribute_id', $colorAttribute->id)
                ->where('name', $variant['color'])
                ->first();
                
            if ($colorValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $colorAttribute->id,
                    'attribute_value_id' => $colorValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createGooglePixel8Pro()
    {
        // Create parent product
        $parent = Product::create([
            'name' => 'جوجل بيكسل 8 برو',
            'description' => 'هاتف ذكي متطور من جوجل مع كاميرا استثنائية وتجربة أندرويد نقية',
            'price' => 899.99,
            'is_parent' => true,
            'parent_id' => null,
        ]);

        // Add product images
        ProductImage::create([
            'product_id' => $parent->id,
            'product_variant_id' => null,
            'image_path' => 'google-pixel8-pro-main.jpg',
            'alt_text' => 'جوجل بيكسل 8 برو',
            'is_primary' => true,
            'image_type' => 'main'
        ]);

        // Create variants
        $variants = [
            [
                'storage' => '128 جيجابايت',
                'color' => 'أسود',
                'price' => 899.99,
                'sku' => 'GP8P-128-OBS',
                'stock' => 75,
                'is_default' => true,
            ],
            [
                'storage' => '256 جيجابايت',
                'color' => 'أسود',
                'price' => 999.99,
                'sku' => 'GP8P-256-OBS',
                'stock' => 60,
                'is_default' => false,
            ],
            [
                'storage' => '512 جيجابايت',
                'color' => 'أسود',
                'price' => 1099.99,
                'sku' => 'GP8P-512-OBS',
                'stock' => 45,
                'is_default' => false,
            ],
            [
                'storage' => '128 جيجابايت',
                'color' => 'أبيض خزفي',
                'price' => 899.99,
                'sku' => 'GP8P-128-POR',
                'stock' => 70,
                'is_default' => false,
            ],
            [
                'storage' => '256 جيجابايت',
                'color' => 'أبيض خزفي',
                'price' => 999.99,
                'sku' => 'GP8P-256-POR',
                'stock' => 55,
                'is_default' => false,
            ],
            [
                'storage' => '128 جيجابايت',
                'color' => 'خليجي',
                'price' => 899.99,
                'sku' => 'GP8P-128-BAY',
                'stock' => 65,
                'is_default' => false,
            ],
            [
                'storage' => '256 جيجابايت',
                'color' => 'خليجي',
                'price' => 999.99,
                'sku' => 'GP8P-256-BAY',
                'stock' => 50,
                'is_default' => false,
            ],
        ];

        // Get attribute IDs
        $storageAttribute = Attribute::where('name', 'التخزين')->first();
        $colorAttribute = Attribute::where('name', 'اللون')->first();

        foreach ($variants as $variant) {
            // Create variant title
            $variantTitle = "جوجل بيكسل 8 برو - {$variant['storage']} - {$variant['color']}";
            
            // Create product variant
            $productVariant = ProductVariant::create([
                'product_id' => $parent->id,
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'is_default' => $variant['is_default'],
                'variant_title' => $variantTitle,
            ]);
            
            // Add variant images
            ProductImage::create([
                'product_id' => null,
                'product_variant_id' => $productVariant->id,
                'image_path' => "google-pixel8-pro-{$variant['color']}.jpg",
                'alt_text' => $variantTitle,
                'is_primary' => true,
                'image_type' => 'main'
            ]);
            
            // Assign attributes to variant
            // Storage attribute
            $storageValue = AttributeValue::where('attribute_id', $storageAttribute->id)
                ->where('name', $variant['storage'])
                ->first();
                
            if ($storageValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $storageAttribute->id,
                    'attribute_value_id' => $storageValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Color attribute
            $colorValue = AttributeValue::where('attribute_id', $colorAttribute->id)
                ->where('name', $variant['color'])
                ->first();
                
            if ($colorValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $colorAttribute->id,
                    'attribute_value_id' => $colorValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createXiaomiRedmiNote12()
    {
        // Create parent product
        $parent = Product::create([
            'name' => 'شاومي ريدمي نوت 12',
            'description' => 'هاتف ذكي بسعر معقول مع شاشة AMOLED وبطارية كبيرة وكاميرا متعددة',
            'price' => 179.99,
            'is_parent' => true,
            'parent_id' => null,
        ]);

        // Add product images
        ProductImage::create([
            'product_id' => $parent->id,
            'product_variant_id' => null,
            'image_path' => 'xiaomi-redmi-note12-main.jpg',
            'alt_text' => 'شاومي ريدمي نوت 12',
            'is_primary' => true,
            'image_type' => 'main'
        ]);

        // Create variants
        $variants = [
            [
                'storage' => '128 جيجابايت',
                'ram' => '6 جيجابايت',
                'color' => 'رمادي عقيق',
                'price' => 179.99,
                'sku' => 'XRN12-128-6-OG',
                'stock' => 120,
                'is_default' => true,
            ],
            [
                'storage' => '128 جيجابايت',
                'ram' => '8 جيجابايت',
                'color' => 'رمادي عقيق',
                'price' => 199.99,
                'sku' => 'XRN12-128-8-OG',
                'stock' => 100,
                'is_default' => false,
            ],
            [
                'storage' => '128 جيجابايت',
                'ram' => '6 جيجابايت',
                'color' => 'أزرق ثلجي',
                'price' => 179.99,
                'sku' => 'XRN12-128-6-IB',
                'stock' => 110,
                'is_default' => false,
            ],
            [
                'storage' => '128 جيجابايت',
                'ram' => '8 جيجابايت',
                'color' => 'أزرق ثلجي',
                'price' => 199.99,
                'sku' => 'XRN12-128-8-IB',
                'stock' => 90,
                'is_default' => false,
            ],
        ];

        // Get attribute IDs
        $storageAttribute = Attribute::where('name', 'التخزين')->first();
        $ramAttribute = Attribute::where('name', 'الذاكرة')->first();
        $colorAttribute = Attribute::where('name', 'اللون')->first();

        foreach ($variants as $variant) {
            // Create variant title
            $variantTitle = "شاومي ريدمي نوت 12 - {$variant['storage']} - {$variant['ram']} - {$variant['color']}";
            
            // Create product variant
            $productVariant = ProductVariant::create([
                'product_id' => $parent->id,
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'is_default' => $variant['is_default'],
                'variant_title' => $variantTitle,
            ]);
            
            // Add variant images
            ProductImage::create([
                'product_id' => null,
                'product_variant_id' => $productVariant->id,
                'image_path' => "xiaomi-redmi-note12-{$variant['color']}.jpg",
                'alt_text' => $variantTitle,
                'is_primary' => true,
                'image_type' => 'main'
            ]);
            
            // Assign attributes to variant
            // Storage attribute
            $storageValue = AttributeValue::where('attribute_id', $storageAttribute->id)
                ->where('name', $variant['storage'])
                ->first();
                
            if ($storageValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $storageAttribute->id,
                    'attribute_value_id' => $storageValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // RAM attribute
            $ramValue = AttributeValue::where('attribute_id', $ramAttribute->id)
                ->where('name', $variant['ram'])
                ->first();
                
            if ($ramValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $ramAttribute->id,
                    'attribute_value_id' => $ramValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Color attribute
            $colorValue = AttributeValue::where('attribute_id', $colorAttribute->id)
                ->where('name', $variant['color'])
                ->first();
                
            if ($colorValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $colorAttribute->id,
                    'attribute_value_id' => $colorValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createMacbookPro()
    {
        // Create parent product
        $parent = Product::create([
            'name' => 'ماك بوك برو',
            'description' => 'حاسوب محمول قوي من آبل مع شريحة M2 وشاشة Liquid Retina XDR وعمر بطارية طويل',
            'price' => 1999.99,
            'is_parent' => true,
            'parent_id' => null,
        ]);

        // Add product images
        ProductImage::create([
            'product_id' => $parent->id,
            'product_variant_id' => null,
            'image_path' => 'macbook-pro-main.jpg',
            'alt_text' => 'ماك بوك برو',
            'is_primary' => true,
            'image_type' => 'main'
        ]);

        // Create variants
        $variants = [
            [
                'screen_size' => '14 بوصة',
                'processor' => 'Apple M2 Pro',
                'storage' => '512 جيجابايت',
                'ram' => '16 جيجابايت',
                'color' => 'رمادي فلكي',
                'price' => 1999.99,
                'sku' => 'MBP-14-M2P-512-16-SG',
                'stock' => 50,
                'is_default' => true,
            ],
            [
                'screen_size' => '14 بوصة',
                'processor' => 'Apple M2 Pro',
                'storage' => '1 تيرابايت',
                'ram' => '16 جيجابايت',
                'color' => 'رمادي فلكي',
                'price' => 2199.99,
                'sku' => 'MBP-14-M2P-1TB-16-SG',
                'stock' => 40,
                'is_default' => false,
            ],
            [
                'screen_size' => '14 بوصة',
                'processor' => 'Apple M2 Max',
                'storage' => '1 تيرابايت',
                'ram' => '32 جيجابايت',
                'color' => 'رمادي فلكي',
                'price' => 2899.99,
                'sku' => 'MBP-14-M2M-1TB-32-SG',
                'stock' => 30,
                'is_default' => false,
            ],
            [
                'screen_size' => '16 بوصة',
                'processor' => 'Apple M2 Pro',
                'storage' => '512 جيجابايت',
                'ram' => '16 جيجابايت',
                'color' => 'رمادي فلكي',
                'price' => 2499.99,
                'sku' => 'MBP-16-M2P-512-16-SG',
                'stock' => 45,
                'is_default' => false,
            ],
            [
                'screen_size' => '16 بوصة',
                'processor' => 'Apple M2 Pro',
                'storage' => '1 تيرابايت',
                'ram' => '16 جيجابايت',
                'color' => 'رمادي فلكي',
                'price' => 2699.99,
                'sku' => 'MBP-16-M2P-1TB-16-SG',
                'stock' => 35,
                'is_default' => false,
            ],
            [
                'screen_size' => '16 بوصة',
                'processor' => 'Apple M2 Max',
                'storage' => '1 تيرابايت',
                'ram' => '32 جيجابايت',
                'color' => 'رمادي فلكي',
                'price' => 3499.99,
                'sku' => 'MBP-16-M2M-1TB-32-SG',
                'stock' => 25,
                'is_default' => false,
            ],
        ];

        // Get attribute IDs
        $screenSizeAttribute = Attribute::where('name', 'حجم الشاشة')->first();
        $processorAttribute = Attribute::where('name', 'المعالج')->first();
        $storageAttribute = Attribute::where('name', 'التخزين')->first();
        $ramAttribute = Attribute::where('name', 'الذاكرة')->first();
        $colorAttribute = Attribute::where('name', 'اللون')->first();

        foreach ($variants as $variant) {
            // Create variant title
            $variantTitle = "ماك بوك برو - {$variant['screen_size']} - {$variant['processor']} - {$variant['storage']} - {$variant['ram']}";
            
            // Create product variant
            $productVariant = ProductVariant::create([
                'product_id' => $parent->id,
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'is_default' => $variant['is_default'],
                'variant_title' => $variantTitle,
            ]);
            
            // Add variant images
            ProductImage::create([
                'product_id' => null,
                'product_variant_id' => $productVariant->id,
                'image_path' => "macbook-pro-{$variant['screen_size']}.jpg",
                'alt_text' => $variantTitle,
                'is_primary' => true,
                'image_type' => 'main'
            ]);
            
            // Assign attributes to variant
            // Screen Size attribute
            $screenSizeValue = AttributeValue::where('attribute_id', $screenSizeAttribute->id)
                ->where('name', $variant['screen_size'])
                ->first();
                
            if ($screenSizeValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $screenSizeAttribute->id,
                    'attribute_value_id' => $screenSizeValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Processor attribute
            $processorValue = AttributeValue::where('attribute_id', $processorAttribute->id)
                ->where('name', $variant['processor'])
                ->first();
                
            if ($processorValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $processorAttribute->id,
                    'attribute_value_id' => $processorValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Storage attribute
            $storageValue = AttributeValue::where('attribute_id', $storageAttribute->id)
                ->where('name', $variant['storage'])
                ->first();
                
            if ($storageValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $storageAttribute->id,
                    'attribute_value_id' => $storageValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // RAM attribute
            $ramValue = AttributeValue::where('attribute_id', $ramAttribute->id)
                ->where('name', $variant['ram'])
                ->first();
                
            if ($ramValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $ramAttribute->id,
                    'attribute_value_id' => $ramValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Color attribute
            $colorValue = AttributeValue::where('attribute_id', $colorAttribute->id)
                ->where('name', $variant['color'])
                ->first();
                
            if ($colorValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $colorAttribute->id,
                    'attribute_value_id' => $colorValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createDellXPS15()
    {
        // Create parent product
        $parent = Product::create([
            'name' => 'ديل إكس بي إس 15',
            'description' => 'حاسوب محمول ويندوز متميز مع شاشة مذهلة وأداء قوي وتصميم أنيق للمحترفين والمبدعين',
            'price' => 1699.99,
            'is_parent' => true,
            'parent_id' => null,
        ]);

        // Add product images
        ProductImage::create([
            'product_id' => $parent->id,
            'product_variant_id' => null,
            'image_path' => 'dell-xps15-main.jpg',
            'alt_text' => 'ديل إكس بي إس 15',
            'is_primary' => true,
            'image_type' => 'main'
        ]);

        // Create variants
        $variants = [
            [
                'processor' => 'i7',
                'storage' => '512 جيجابايت',
                'ram' => '16 جيجابايت',
                'graphics' => 'RTX 3050',
                'price' => 1699.99,
                'sku' => 'DXPS15-i7-512-16-3050',
                'stock' => 60,
                'is_default' => true,
            ],
            [
                'processor' => 'i7',
                'storage' => '1 تيرابايت',
                'ram' => '16 جيجابايت',
                'graphics' => 'RTX 3050',
                'price' => 1899.99,
                'sku' => 'DXPS15-i7-1TB-16-3050',
                'stock' => 50,
                'is_default' => false,
            ],
            [
                'processor' => 'i7',
                'storage' => '1 تيرابايت',
                'ram' => '32 جيجابايت',
                'graphics' => 'RTX 3050 Ti',
                'price' => 2099.99,
                'sku' => 'DXPS15-i7-1TB-32-3050Ti',
                'stock' => 40,
                'is_default' => false,
            ],
            [
                'processor' => 'i9',
                'storage' => '1 تيرابايت',
                'ram' => '32 جيجابايت',
                'graphics' => 'RTX 3050 Ti',
                'price' => 2299.99,
                'sku' => 'DXPS15-i9-1TB-32-3050Ti',
                'stock' => 35,
                'is_default' => false,
            ],
            [
                'processor' => 'i9',
                'storage' => '2 تيرابايت',
                'ram' => '64 جيجابايت',
                'graphics' => 'RTX 3050 Ti',
                'price' => 2699.99,
                'sku' => 'DXPS15-i9-2TB-64-3050Ti',
                'stock' => 25,
                'is_default' => false,
            ],
        ];

        // Get attribute IDs
        $processorAttribute = Attribute::where('name', 'المعالج')->first();
        $storageAttribute = Attribute::where('name', 'التخزين')->first();
        $ramAttribute = Attribute::where('name', 'الذاكرة')->first();
        $graphicsAttribute = Attribute::where('name', 'كرت الشاشة')->first();

        foreach ($variants as $variant) {
            // Create variant title
            $variantTitle = "ديل إكس بي إس 15 - {$variant['processor']} - {$variant['storage']} - {$variant['ram']} - {$variant['graphics']}";
            
            // Create product variant
            $productVariant = ProductVariant::create([
                'product_id' => $parent->id,
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'is_default' => $variant['is_default'],
                'variant_title' => $variantTitle,
            ]);
            
            // Add variant images
            ProductImage::create([
                'product_id' => null,
                'product_variant_id' => $productVariant->id,
                'image_path' => "dell-xps15-{$variant['processor']}.jpg",
                'alt_text' => $variantTitle,
                'is_primary' => true,
                'image_type' => 'main'
            ]);
            
            // Assign attributes to variant
            // Processor attribute
            $processorValue = AttributeValue::where('attribute_id', $processorAttribute->id)
                ->where('name', $variant['processor'])
                ->first();
                
            if ($processorValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $processorAttribute->id,
                    'attribute_value_id' => $processorValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Storage attribute
            $storageValue = AttributeValue::where('attribute_id', $storageAttribute->id)
                ->where('name', $variant['storage'])
                ->first();
                
            if ($storageValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $storageAttribute->id,
                    'attribute_value_id' => $storageValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // RAM attribute
            $ramValue = AttributeValue::where('attribute_id', $ramAttribute->id)
                ->where('name', $variant['ram'])
                ->first();
                
            if ($ramValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $ramAttribute->id,
                    'attribute_value_id' => $ramValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Graphics attribute
            $graphicsValue = AttributeValue::where('attribute_id', $graphicsAttribute->id)
                ->where('name', $variant['graphics'])
                ->first();
                
            if ($graphicsValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $graphicsAttribute->id,
                    'attribute_value_id' => $graphicsValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createLenovoThinkpad()
    {
        // Create parent product
        $parent = Product::create([
            'name' => 'Lenovo ThinkPad X1 Carbon',
            'description' => 'Business-class ultrabook with legendary durability, security features, and exceptional keyboard for professionals.',
            'price' => 1499.99,
            'is_parent' => true,
            'parent_id' => null,
        ]);

        // Add product images
        ProductImage::create([
            'product_id' => $parent->id,
            'product_variant_id' => null,
            'image_path' => 'lenovo-thinkpad-x1-main.jpg',
            'alt_text' => 'Lenovo ThinkPad X1 Carbon',
            'is_primary' => true,
            'image_type' => 'main'
        ]);

        // Create variants
        $variants = [
            [
                'processor' => 'i5',
                'storage' => '256GB',
                'ram' => '16GB',
                'color' => 'Black',
                'price' => 1499.99,
                'sku' => 'LTP-X1C-i5-256-16-BLK',
                'stock' => 50,
                'is_default' => true,
            ],
            [
                'processor' => 'i5',
                'storage' => '512GB',
                'ram' => '16GB',
                'color' => 'Black',
                'price' => 1599.99,
                'sku' => 'LTP-X1C-i5-512-16-BLK',
                'stock' => 45,
                'is_default' => false,
            ],
            [
                'processor' => 'i7',
                'storage' => '512GB',
                'ram' => '16GB',
                'color' => 'Black',
                'price' => 1799.99,
                'sku' => 'LTP-X1C-i7-512-16-BLK',
                'stock' => 40,
                'is_default' => false,
            ],
            [
                'processor' => 'i7',
                'storage' => '1TB',
                'ram' => '32GB',
                'color' => 'Black',
                'price' => 1999.99,
                'sku' => 'LTP-X1C-i7-1TB-32-BLK',
                'stock' => 30,
                'is_default' => false,
            ],
        ];

        // Get attribute IDs
        $processorAttribute = Attribute::where('name', 'المعالج')->first();
        $storageAttribute = Attribute::where('name', 'التخزين')->first();
        $ramAttribute = Attribute::where('name', 'الذاكرة')->first();
        $colorAttribute = Attribute::where('name', 'اللون')->first();

        foreach ($variants as $variant) {
            // Create variant title
            $variantTitle = "Lenovo ThinkPad X1 Carbon - {$variant['processor']} - {$variant['storage']} - {$variant['ram']}";
            
            // Create product variant
            $productVariant = ProductVariant::create([
                'product_id' => $parent->id,
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'is_default' => $variant['is_default'],
                'variant_title' => $variantTitle,
            ]);
            
            // Add variant images
            ProductImage::create([
                'product_id' => null,
                'product_variant_id' => $productVariant->id,
                'image_path' => "lenovo-thinkpad-x1-{$variant['color']}.jpg",
                'alt_text' => $variantTitle,
                'is_primary' => true,
                'image_type' => 'main'
            ]);
            
            // Assign attributes to variant
            // Processor attribute
            $processorValue = AttributeValue::where('attribute_id', $processorAttribute->id)
                ->where('name', $variant['processor'])
                ->first();
                
            if ($processorValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $processorAttribute->id,
                    'attribute_value_id' => $processorValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Storage attribute
            $storageValue = AttributeValue::where('attribute_id', $storageAttribute->id)
                ->where('name', $variant['storage'])
                ->first();
                
            if ($storageValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $storageAttribute->id,
                    'attribute_value_id' => $storageValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // RAM attribute
            $ramValue = AttributeValue::where('attribute_id', $ramAttribute->id)
                ->where('name', $variant['ram'])
                ->first();
                
            if ($ramValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $ramAttribute->id,
                    'attribute_value_id' => $ramValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Color attribute
            $colorValue = AttributeValue::where('attribute_id', $colorAttribute->id)
                ->where('name', $variant['color'])
                ->first();
                
            if ($colorValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $colorAttribute->id,
                    'attribute_value_id' => $colorValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createSonyWH1000XM5()
    {
        // Create parent product
        $parent = Product::create([
            'name' => 'Sony WH-1000XM5',
            'description' => 'Industry-leading noise cancelling headphones with exceptional sound quality and long battery life.',
            'price' => 399.99,
            'is_parent' => true,
            'parent_id' => null,
        ]);

        // Add product images
        ProductImage::create([
            'product_id' => $parent->id,
            'product_variant_id' => null,
            'image_path' => 'sony-wh1000xm5-main.jpg',
            'alt_text' => 'Sony WH-1000XM5',
            'is_primary' => true,
            'image_type' => 'main'
        ]);

        // Create variants
        $variants = [
            [
                'color' => 'أسود',
                'price' => 399.99,
                'sku' => 'SONY-WH1000XM5-BLK',
                'stock' => 75,
                'is_default' => true,
            ],
            [
                'color' => 'فضي',
                'price' => 399.99,
                'sku' => 'SONY-WH1000XM5-SLV',
                'stock' => 65,
                'is_default' => false,
            ],
            [
                'color' => 'ذهبي',
                'price' => 419.99,
                'sku' => 'SONY-WH1000XM5-MDBL',
                'stock' => 45,
                'is_default' => false,
            ],
        ];

        // Get attribute IDs
        $colorAttribute = Attribute::where('name', 'اللون')->first();

        foreach ($variants as $variant) {
            // Create variant title
            $variantTitle = "Sony WH-1000XM5 - {$variant['color']}";
            
            // Create product variant
            $productVariant = ProductVariant::create([
                'product_id' => $parent->id,
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'is_default' => $variant['is_default'],
                'variant_title' => $variantTitle,
            ]);
            
            // Add variant images
            ProductImage::create([
                'product_id' => null,
                'product_variant_id' => $productVariant->id,
                'image_path' => "sony-wh1000xm5-{$variant['color']}.jpg",
                'alt_text' => $variantTitle,
                'is_primary' => true,
                'image_type' => 'main'
            ]);
            
            // Assign attributes to variant
            // Color attribute
            $colorValue = AttributeValue::where('attribute_id', $colorAttribute->id)
                ->where('name', $variant['color'])
                ->first();
                
            if ($colorValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $colorAttribute->id,
                    'attribute_value_id' => $colorValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createAirPodsPro()
    {
        // Create parent product
        $parent = Product::create([
            'name' => 'Apple AirPods Pro (2nd Generation)',
            'description' => 'Active Noise Cancellation, Transparency mode, Personalized Spatial Audio, and superior comfort.',
            'price' => 249.99,
            'is_parent' => true,
            'parent_id' => null,
        ]);

        // Add product images
        ProductImage::create([
            'product_id' => $parent->id,
            'product_variant_id' => null,
            'image_path' => 'airpods-pro-main.jpg',
            'alt_text' => 'Apple AirPods Pro',
            'is_primary' => true,
            'image_type' => 'main'
        ]);

        // Create variants
        $variants = [
            [
                'case_type' => 'USB-C Charging Case',
                'price' => 249.99,
                'sku' => 'APP2-USBC',
                'stock' => 100,
                'is_default' => true,
            ],
            [
                'case_type' => 'MagSafe Charging Case',
                'price' => 269.99,
                'sku' => 'APP2-MAGSAFE',
                'stock' => 85,
                'is_default' => false,
            ],
            [
                'case_type' => 'Wireless Charging Case with Engraving',
                'price' => 289.99,
                'sku' => 'APP2-WIRELESS-ENG',
                'stock' => 60,
                'is_default' => false,
            ],
        ];

        // Get attribute IDs
        $caseTypeAttribute = Attribute::where('name', 'نوع العلبة')->first();

        foreach ($variants as $variant) {
            // Create variant title
            $variantTitle = "Apple AirPods Pro (2nd Generation) - {$variant['case_type']}";
            
            // Create product variant
            $productVariant = ProductVariant::create([
                'product_id' => $parent->id,
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'is_default' => $variant['is_default'],
                'variant_title' => $variantTitle,
            ]);
            
            // Add variant images
            ProductImage::create([
                'product_id' => null,
                'product_variant_id' => $productVariant->id,
                'image_path' => "airpods-pro-{$variant['case_type']}.jpg",
                'alt_text' => $variantTitle,
                'is_primary' => true,
                'image_type' => 'main'
            ]);
            
            // Assign attributes to variant
            // Case Type attribute
            $caseTypeValue = AttributeValue::where('attribute_id', $caseTypeAttribute->id)
                ->where('name', $variant['case_type'])
                ->first();
                
            if ($caseTypeValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $caseTypeAttribute->id,
                    'attribute_value_id' => $caseTypeValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createBoseSoundSport()
    {
        // Create parent product
        $parent = Product::create([
            'name' => 'Bose SoundSport Wireless',
            'description' => 'Sweat and weather-resistant sports earbuds with balanced sound and secure fit for active lifestyles.',
            'price' => 129.99,
            'is_parent' => true,
            'parent_id' => null,
        ]);

        // Add product images
        ProductImage::create([
            'product_id' => $parent->id,
            'product_variant_id' => null,
            'image_path' => 'bose-soundsport-main.jpg',
            'alt_text' => 'Bose SoundSport Wireless',
            'is_primary' => true,
            'image_type' => 'main'
        ]);

        // Create variants
        $variants = [
            [
                'color' => 'أسود',
                'price' => 129.99,
                'sku' => 'BOSE-SS-BLK',
                'stock' => 60,
                'is_default' => true,
            ],
            [
                'color' => 'أزرق',
                'price' => 129.99,
                'sku' => 'BOSE-SS-AQA',
                'stock' => 50,
                'is_default' => false,
            ],
            [
                'color' => 'رمادي',
                'price' => 129.99,
                'sku' => 'BOSE-SS-CIT',
                'stock' => 45,
                'is_default' => false,
            ],
            [
                'color' => 'أخضر',
                'price' => 139.99,
                'sku' => 'BOSE-SS-RED',
                'stock' => 40,
                'is_default' => false,
            ],
        ];

        // Get attribute IDs
        $colorAttribute = Attribute::where('name', 'اللون')->first();

        foreach ($variants as $variant) {
            // Create variant title
            $variantTitle = "Bose SoundSport Wireless - {$variant['color']}";
            
            // Create product variant
            $productVariant = ProductVariant::create([
                'product_id' => $parent->id,
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'is_default' => $variant['is_default'],
                'variant_title' => $variantTitle,
            ]);
            
            // Add variant images
            ProductImage::create([
                'product_id' => null,
                'product_variant_id' => $productVariant->id,
                'image_path' => "bose-soundsport-{$variant['color']}.jpg",
                'alt_text' => $variantTitle,
                'is_primary' => true,
                'image_type' => 'main'
            ]);
            
            // Assign attributes to variant
            // Color attribute
            $colorValue = AttributeValue::where('attribute_id', $colorAttribute->id)
                ->where('name', $variant['color'])
                ->first();
                
            if ($colorValue) {
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $productVariant->id,
                    'attribute_id' => $colorAttribute->id,
                    'attribute_value_id' => $colorValue->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
