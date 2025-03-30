<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

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
    
    /**
     * Change customer password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        // Log password change request
        Log::info('Password change requested via ProfileController');

        // Validate the request
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully'
        ], Response::HTTP_OK);
    }
}