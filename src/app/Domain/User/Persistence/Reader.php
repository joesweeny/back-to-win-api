<?php

namespace GamePlatform\Domain\User\Persistence;

use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Uuid\Uuid;

interface Reader
{
    /**
     * Retrieves a User by their email
     *
     * @param string $email
     * @return User
     * @throws NotFoundException
     */
    public function getByEmail(string $email): User;

    /**
     * Retrieve a User by their ID
     *
     * @param Uuid $id
     * @return User
     * @throws NotFoundException
     */
    public function getById(Uuid $id): User;

    /**
     * Retrieves a User by their username
     *
     * @param string $username
     * @return User
     * @throws NotFoundException
     */
    public function getByUsername(string $username): User;

    /**
     * Return a collection of all Users
     *
     * @return array
     */
    public function getUsers(): array;
}
