<?php

namespace App\Manager;


use App\Entity\User;
use App\Manager\Contract\UserManager as UserManagerContract;
use App\Request\Contract\SaveUserRequest;
use Illuminate\Support\Collection;

class UserManager implements UserManagerContract
{

    /**
     * Find All Users
     *
     * @return Collection
     */
    public function findAll(): Collection
    {
        return User::all();
    }

    /**
     * Get User model by ID
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id)
    {
        return User::find($id) ?? null;
    }

    /**
     * Find Users that has is_active flag true
     *
     * @return Collection
     */
    public function findActive(): Collection
    {
        return User::where('is_active', 1)->get();
    }

    /**
     * Create or Update User
     *
     * @param SaveUserRequest $request
     * @return User
     */
    public function saveUser(SaveUserRequest $request): User
    {
        return User::updateOrCreate(
            ['id' => $request->getUser()->id],
            [
                'first_name' => $request->getFirstName(),
                'last_name' => $request->getLastName(),
                'is_active' => $request->getIsActive(),
            ]
        );
    }

    /**
     * Delete User By ID
     *
     * @param int $userId
     * @return void
     */
    public function deleteUser(int $userId)
    {
        User::destroy($userId);
    }
}