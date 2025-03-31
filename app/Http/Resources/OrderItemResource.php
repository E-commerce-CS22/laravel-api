<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => new SimplifiedProductResource($this->whenLoaded('product')),
            'product_variant_id' => $this->product_variant_id,
            'product_variant' => $this->when($this->product_variant_id, function () {
                return new SimplifiedProductVariantResource($this->productVariant);
            }),
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
        ];
    }
}
