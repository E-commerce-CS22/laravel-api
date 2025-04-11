<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'discount_start_date' => $this->discount_start_date,
            'discount_end_date' => $this->discount_end_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image' => $this->image,
            'tags' => $this->whenLoaded('tags'),
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($variant) {
                    $attributeData = [];
                    
                    // Process attribute values for this variant
                    foreach ($variant->attributeValues as $attributeValue) {
                        $attributeData[] = [
                            'attribute' => [
                                'id' => $attributeValue->attribute->id,
                                'name' => $attributeValue->attribute->name,
                            ],
                            'value' => [
                                'id' => $attributeValue->id,
                                'name' => $attributeValue->name,
                            ]
                        ];
                    }
                    
                    return [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                        'extra_price' => $variant->extra_price,
                        'stock' => $variant->stock,
                        'is_default' => $variant->is_default,
                        'variant_title' => $variant->variant_title,
                        'images' => $variant->images,
                        'attributes' => $attributeData
                    ];
                });
            })
        ];
    }
}
