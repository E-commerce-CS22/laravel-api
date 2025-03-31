<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SimplifiedProductVariantResource extends JsonResource
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
            'price' => (float) $this->price,
            'extra_price' => (float) $this->extra_price,
            'stock' => (int) $this->stock,
            'is_default' => (bool) $this->is_default,
            'variant_title' => $this->variant_title,
        ];
    }
}
