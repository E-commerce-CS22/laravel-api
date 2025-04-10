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
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'profile' => $this->profile,
        ];

        // Add customer-specific data if the user is a customer
        if ($this->role === 'customer') {
            $data['customer_data'] = [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone' => $this->phone,
                'address' => $this->address,
                'city' => $this->city,
                'country' => $this->country,
                'cart_id' => $this->cart_id,
                'wishlist_id' => $this->wishlist_id,
            ];
        }

        return $data;
    }
}