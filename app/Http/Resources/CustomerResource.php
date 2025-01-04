<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'profile_image' => $this->profile_image ? asset('storage/' . $this->profile_image) : null,
            'first_name' => $this->customer->first_name,
            'last_name' => $this->customer->last_name,
            'phone' => $this->customer->phone,
            'address' => $this->customer->address,
            'city' => $this->customer->city,
            'postal_code' => $this->customer->postal_code,
            'country' => $this->customer->country,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}