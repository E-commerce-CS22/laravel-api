<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user is a customer
        if (!$user->isCustomer()) {
            return response()->json([
                'message' => 'Only customers can update their profile'
            ], Response::HTTP_FORBIDDEN);
        }
        
        $userData = $request->only([
            'username', 'email', 'first_name', 'last_name', 'phone', 
            'address', 'city', 'country'
        ]);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $userData['profile'] = $path;

            // Delete old profile image if exists
            if ($user->profile) {
                Storage::disk('public')->delete($user->profile);
            }
        }

        $user->fill($userData);
        $user->save();

        return new UserResource($user);
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
        
        // Ensure user is a customer
        if (!$user->isCustomer()) {
            return response()->json([
                'message' => 'Only customers can change their password through this endpoint'
            ], Response::HTTP_FORBIDDEN);
        }

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