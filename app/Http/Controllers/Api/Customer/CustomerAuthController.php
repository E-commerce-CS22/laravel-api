<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishListController;
use App\Services\CartService;
use App\Services\WishListService;

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
        $user = $request->user();
        
        // Ensure user is a customer
        if (!$user->isCustomer()) {
            return response()->json([
                'message' => 'Unauthorized. Only customers can access this endpoint.'
            ], Response::HTTP_FORBIDDEN);
        }

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
            'country' => 'nullable|string',
            'profile' => 'nullable|string',
        ]);

        // Ensure all required fields are provided
        if (!$request->has(['first_name', 'last_name', 'phone', 'address', 'city'])) {
            return response()->json([
                'message' => 'Missing required customer information.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Create cart and wishlist
        $cartController = new CartController(app()->make(CartService::class));
        $cart = $cartController->create();
        
        $wishListController = new WishListController(app()->make(WishListService::class));
        $wishList = $wishListController->create();

        // Create user with customer role
        $user = User::create([
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'profile' => $request->profile, // Save profile if provided
            'status' => 'active', // New customers start as active
            'role' => 'customer',
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country ?? 'اليمن', // Default to Yemen if not provided
            'cart_id' => $cart->id,
            'wishlist_id' => $wishList->id,
        ]);

        // Create token with customer ability
        $token = $user->createToken('customer-token', ['customer']);

        return response()->json([
            'message' => 'Registration successful.',
            'user' => new UserResource($user),
            'token' => $token->plainTextToken
        ], Response::HTTP_CREATED);
    }
}
