<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Add customer data if exists
        if ($this->customer) {
            $data['customer'] = [
                'first_name' => $this->customer->first_name,
                'last_name' => $this->customer->last_name,
                'phone' => $this->customer->phone,
                'address' => $this->customer->address,
                'city' => $this->customer->city,
                'postal_code' => $this->customer->postal_code,
                'cart_id' => $this->customer->cart_id,
                'wishlist_id' => $this->customer->wishlist_id,
            ];
        }

        // Add admin data if exists
        if ($this->admin) {
            $data['admin'] = [
                'name' => $this->admin->name,
            ];
        }

        return $data;
    }
}
