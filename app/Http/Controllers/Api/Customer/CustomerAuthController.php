<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuthController extends Controller
{
    /**
     * Customer login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Check if user is a customer
        if (!$user->customer) {
            return response()->json([
                'message' => 'Invalid account type. Please use the customer app to login.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Create token with customer ability
        $token = $user->createToken('customer-token', ['customer']);

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'token' => $token->plainTextToken
        ], Response::HTTP_OK);
    }

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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'username' => 'required|string|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'nullable|string',
        ]);

        // Create user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'inactive', // New customers start as inactive
        ]);

        // Create customer profile
        $user->customer()->create([
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
        ]);

        // Create token with customer ability
        $token = $user->createToken('customer-token', ['customer']);

        return response()->json([
            'message' => 'Registration successful. Your account is pending activation.',
            'user' => new UserResource($user),
            'token' => $token->plainTextToken
        ], Response::HTTP_CREATED);
    }
}
