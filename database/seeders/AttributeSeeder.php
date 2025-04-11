<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create attributes in the exact order required by AttributeValueSeeder
        $color = $this->createAttributeIfNotExists('اللون'); // ID=1
        $size = $this->createAttributeIfNotExists('الحجم');  // ID=2
        $material = $this->createAttributeIfNotExists('المواد'); // ID=3
        $storage = $this->createAttributeIfNotExists('التخزين'); // ID=4
        $ram = $this->createAttributeIfNotExists('الذاكرة'); // ID=5
        $processor = $this->createAttributeIfNotExists('المعالج'); // ID=6
        $placeholder4 = $this->createAttributeIfNotExists('دقة الشاشة');  // ID=7
        $screenSize = $this->createAttributeIfNotExists('حجم الشاشة'); // ID=8
        $battery = $this->createAttributeIfNotExists('سعة البطارية'); // ID=9
        $graphics = $this->createAttributeIfNotExists('كرت الشاشة'); // ID=10
    }
    
    /**
     * Create attribute if it doesn't exist
     */
    private function createAttributeIfNotExists(string $name): Attribute
    {
        $attribute = Attribute::where('name', $name)->first();
        
        if (!$attribute) {
            $attribute = Attribute::create(['name' => $name]);
        }
        
        return $attribute;
    }
    
    /**
     * Create attribute value if it doesn't exist
     */
    private function createAttributeValueIfNotExists(int $attributeId, string $name): AttributeValue
    {
        $attributeValue = AttributeValue::where('attribute_id', $attributeId)
            ->where('name', $name)
            ->first();
        
        if (!$attributeValue) {
            $attributeValue = AttributeValue::create([
                'attribute_id' => $attributeId,
                'name' => $name
            ]);
        }
        
        return $attributeValue;
    }
}
