<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserStatusRequest;
use Symfony\Component\HttpFoundation\Response;

class UserManagementController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Check if the user is an admin
     *
     * @param Request $request
     * @return bool
     */
    private function isAdmin(Request $request)
    {
        return $request->user() && $request->user()->isAdmin();
    }

    /**
     * Get list of all users (customers and admins)
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $users = $this->userService->getAllUsers();
            return UserResource::collection($users);
        } catch (\Exception $e) {
            \Log::error('Error in UserManagementController@index: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'message' => 'An error occurred while fetching users',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update user status
     *
     * @param UpdateUserStatusRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(UpdateUserStatusRequest $request, User $user)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $this->userService->updateStatus($user, $request->status);
            
            return response()->json([
                'message' => 'User status updated successfully',
                'data' => new UserResource($user)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Error in UserManagementController@updateStatus: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'An error occurred while updating user status',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
