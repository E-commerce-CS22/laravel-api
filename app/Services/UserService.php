<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users including customers and admins
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsers()
    {
        return $this->userRepository->getAllUsers();
    }

    /**
     * Update user status
     *
     * @param User $user
     * @param string $status
     * @return User
     */
    public function updateStatus(User $user, string $status)
    {
        return $this->userRepository->updateStatus($user, $status);
    }
}
