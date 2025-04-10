<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'extra_price' => (float) $this->extra_price,
            'stock' => (int) $this->stock,
            'is_default' => (bool) $this->is_default,
            'variant_title' => $this->variant_title,
            'attributes' => $this->whenLoaded('attributes', function() {
                return $this->attributes->map(function($attribute) {
                    return [
                        'attribute_id' => $attribute->id,
                        'attribute_name' => $attribute->name,
                        'value_id' => $attribute->pivot->attribute_value_id,
                        'value_name' => $attribute->values->where('id', $attribute->pivot->attribute_value_id)->first()->value ?? null
                    ];
                });
            }),
            'images' => $this->whenLoaded('images', function() {
                return $this->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'url' => url('storage/' . $image->path),
                        'alt_text' => $image->alt_text,
                        'is_primary' => (bool) $image->is_primary,
                        'sort_order' => (int) $image->sort_order,
                        'image_type' => $image->image_type
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
