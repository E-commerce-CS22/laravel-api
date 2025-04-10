<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * User login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Check if user is active
        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Your account is not active. Please contact support.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Create token based on user role
        if ($user->isCustomer()) {
            $token = $user->createToken('customer-token', ['customer']);
        } elseif ($user->isAdmin()) {
            $token = $user->createToken('admin-token', ['admin']);
        } else {
            return response()->json([
                'message' => 'Unauthorized. Invalid user role.'
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token->plainTextToken
        ]);
    }
}