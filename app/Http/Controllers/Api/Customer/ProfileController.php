<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $customer = Auth::user();
        return new CustomerResource($customer);
    }

    public function update(Request $request)
    {
        $customer = Auth::user();
        $userData = $request->only(['username', 'email']);
        $customerData = $request->only([
            'first_name', 'last_name', 'phone', 
            'address', 'city', 'postal_code', 'country'
        ]);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $userData['profile_image'] = $path;

            // Delete old profile image if exists
            if ($customer->profile_image) {
                Storage::disk('public')->delete($customer->profile_image);
            }
        }

        $customer->fill($userData);
        $customer->save();

        // Update customer-specific data
        $customer->customer->fill($customerData);
        $customer->customer->save();

        return new CustomerResource($customer);
    }
}