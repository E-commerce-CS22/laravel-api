<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        // Define attributes
        $attributes = [
            [
                'name' => 'اللون',
                'values' => [
                    ['name' => 'أحمر'],
                    ['name' => 'أزرق'],
                    ['name' => 'أسود'],
                    ['name' => 'أبيض'],
                ]
            ],
            [
                'name' => 'المقاس',
                'values' => [
                    ['name' => 'صغير'],
                    ['name' => 'وسط'],
                    ['name' => 'كبير'],
                    ['name' => 'كبير جداً'],
                ]
            ],
            [
                'name' => 'الخامة',
                'values' => [
                    ['name' => 'قطن'],
                    ['name' => 'بوليستر'],
                    ['name' => 'صوف'],
                ]
            ],
        ];

        // Create attributes and their values
        foreach ($attributes as $attributeData) {
            $attribute = Attribute::create([
                'name' => $attributeData['name']
            ]);

            foreach ($attributeData['values'] as $valueData) {
                AttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'name' => $valueData['name']
                ]);
            }
        }

        // Sample product variants
        $productVariants = [
            [
                'product_id' => Product::first()->id, // Get the first product (قميص قطني كلاسيكي)
                'sku' => 'TSH-RED-S-COT',
                'extra_price' => 0,
                'stock' => 25,
                'attributes' => [
                    ['attribute' => 'اللون', 'value' => 'أحمر'],
                    ['attribute' => 'المقاس', 'value' => 'صغير'],
                    ['attribute' => 'الخامة', 'value' => 'قطن'],
                ]
            ],
            [
                'product_id' => Product::first()->id,
                'sku' => 'TSH-BLU-M-POL',
                'extra_price' => 2.00,
                'stock' => 30,
                'attributes' => [
                    ['attribute' => 'اللون', 'value' => 'أزرق'],
                    ['attribute' => 'المقاس', 'value' => 'وسط'],
                    ['attribute' => 'الخامة', 'value' => 'بوليستر'],
                ]
            ],
            [
                'product_id' => Product::first()->id,
                'sku' => 'TSH-BLK-L-WOL',
                'extra_price' => 7.00,
                'stock' => 15,
                'attributes' => [
                    ['attribute' => 'اللون', 'value' => 'أسود'],
                    ['attribute' => 'المقاس', 'value' => 'كبير'],
                    ['attribute' => 'الخامة', 'value' => 'صوف'],
                ]
            ],
        ];

        // Create product variants and their attribute relationships
        foreach ($productVariants as $variantData) {
            $variant = ProductVariant::create([
                'product_id' => $variantData['product_id'],
                'sku' => $variantData['sku'],
                'extra_price' => $variantData['extra_price'],
                'stock' => $variantData['stock'],
            ]);

            // Attach attributes to variant
            foreach ($variantData['attributes'] as $attrData) {
                $attribute = Attribute::where('name', $attrData['attribute'])->first();
                $attributeValue = AttributeValue::where('name', $attrData['value'])
                    ->where('attribute_id', $attribute->id)
                    ->first();

                $variant->attributeValues()->attach($attributeValue->id, [
                    'attribute_id' => $attribute->id
                ]);
            }
        }
    }
}
