<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Get all users with their respective roles
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsers()
    {
        return User::latest()->get();
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
        $user->update(['status' => $status]);
        return $user->fresh();
    }
}
