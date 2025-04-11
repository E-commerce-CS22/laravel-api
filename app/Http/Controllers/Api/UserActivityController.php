<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserActivityController extends Controller
{
    /**
     * Track user activity by updating the last_active timestamp
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackActivity()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        // Update the last_active timestamp to current time
        $user->last_active = Carbon::now();
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'User activity tracked successfully',
            'data' => [
                'user_id' => $user->id,
                'username' => $user->username,
                'last_active' => $user->last_active
            ]
        ]);
    }
}
