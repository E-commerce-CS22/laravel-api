<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\CartController;

class CustomerAuthController extends Controller
{
    /**
     * Customer logout
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Customer registration
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'profile' => 'nullable|string',
        ]);

        // Create user
        $user = User::create([
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'profile' => $request->profile, // Save profile if provided
            'status' => 'active', // New customers start as inactive
        ]);

        // Ensure all required fields are provided
        if (!$request->has(['first_name', 'last_name', 'phone', 'address', 'city'])) {
            return response()->json([
                'message' => 'Missing required customer information.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $customer = $user->customer()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'اليمن', // Default to Yemen if not provided
        ]);

        $cartController = new CartController(app()->make(\App\Services\CartService::class));
        $cart = $cartController->create();
        $customer->cart_id = $cart->id;
        $customer->save();

        // Create token with customer ability
        $token = $user->createToken('customer-token', ['customer']);

        return response()->json([
            'message' => 'Registration successful. Your account is pending activation.',
            'user' => new UserResource($user),
            'token' => $token->plainTextToken
        ], Response::HTTP_CREATED);
    }
}
