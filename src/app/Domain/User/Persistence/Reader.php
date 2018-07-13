<?php

namespace BackToWin\Domain\User\Persistence;

use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;

interface Reader
{
    /**
     * Retrieves a user by their email
     *
     * @param string $email
     * @return User
     * @throws NotFoundException
     */
    public function getUserByEmail(string $email): User;

    /**
     * @param Uuid $id
     * @return User
     * @throws NotFoundException
     */
    public function getUserById(Uuid $id): User;

    /**
     * Return a collection of all Users
     *
     * @return array
     */
    public function getUsers(): array;
}
