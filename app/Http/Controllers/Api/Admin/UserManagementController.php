<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
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
        $this->middleware(['auth:sanctum', 'abilities:admin']);
    }

    /**
     * Get list of all users (customers and admins)
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $users = $this->userService->getAllUsers();
        return UserResource::collection($users);
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
        $this->userService->updateStatus($user, $request->status);
        
        return response()->json([
            'message' => 'User status updated successfully',
            'data' => new UserResource($user)
        ], Response::HTTP_OK);
    }
}
