<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch related data
        $productVariants = ProductVariant::all();
        $attributes = Attribute::all();
        $attributeValues = AttributeValue::all();

        // Validate existence of related data
        if ($productVariants->isEmpty() || $attributes->isEmpty() || $attributeValues->isEmpty()) {
            $this->command->warn('Related tables (product_variants, attributes, attribute_values) must have data before seeding attribute_product_variant.');
            return;
        }

        // Seed the attribute_product_variant table
        foreach ($productVariants as $variant) {
            foreach ($attributes as $attribute) {
                // Find attribute values related to the current attribute
                $relatedValues = $attributeValues->where('attribute_id', $attribute->id);

                if ($relatedValues->isNotEmpty()) {
                    DB::table('attribute_product_variant')->insert([
                        'product_variant_id' => $variant->id,
                        'attribute_id' => $attribute->id,
                        'attribute_value_id' => $relatedValues->random()->id, // Randomly assign an attribute value
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
