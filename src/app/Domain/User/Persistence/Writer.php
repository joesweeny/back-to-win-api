<?php

namespace BackToWin\Domain\User\Persistence;

use BackToWin\Domain\User\Entity\User;

interface Writer
{
    /**
     * Create a new by adding a new User entry into the database
     *
     * @param User $user
     * @return User
     */
    public function createUser(User $user): User;

    /**
     * @param User $user
     * @return User
     */
    public function updateUser(User $user): User;

    /**
     * Deletes a user from the database
     *
     * @param User $user
     * @return void
     */
    public function deleteUser(User $user);
}
