<?php

namespace BackToWin\Domain\User;

use BackToWin\Domain\User\Persistence\Reader;
use BackToWin\Domain\User\Persistence\Writer;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;

class UserOrchestrator
{
    /**
     * @var Writer
     */
    private $writer;
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Writer $writer, Reader $reader)
    {
        $this->writer = $writer;
        $this->reader = $reader;
    }

    /**
     * @param User $user
     * @return User
     */
    public function createUser(User $user): User
    {
        return $this->writer->insert($user);
    }

    /**
     * @param string $email
     * @return User
     * @throws NotFoundException
     */
    public function getUserByEmail(string $email): User
    {
        return $this->reader->getByEmail($email);
    }

    /**
     * @param Uuid $id
     * @return User
     * @throws \BackToWin\Framework\Exception\NotFoundException
     */
    public function getUserById(Uuid $id): User
    {
        return $this->reader->getById($id);
    }

    /**
     * @param User $user
     * @throws NotFoundException
     * @return User
     */
    public function updateUser(User $user): User
    {
        return $this->writer->update($user);
    }

    /**
     * @param User $user
     * @return void
     */
    public function deleteUser(User $user)
    {
       $this->writer->delete($user);
    }

    /**
     * @param User $user
     * @return bool
     * @throws \BackToWin\Framework\Exception\UndefinedException
     */
    public function canCreateNewUser(User $user): bool
    {
        try {
            $this->getUserByEmail($user->getEmail());
            return false;
        } catch (NotFoundException $e) {
            return true;
        }
    }

    /**
     * @param string $email
     * @return bool
     */
    public function canUpdateUser(string $email): bool
    {
        try {
            $this->getUserByEmail($email);
            return false;
        } catch (NotFoundException $e) {
            return true;
        }
    }

    /**
     * @param Uuid $id
     * @param string $password
     * @return bool
     * @throws \BackToWin\Framework\Exception\UndefinedException
     * @throws \BackToWin\Framework\Exception\NotFoundException
     */
    public function validateUserPassword(Uuid $id, string $password): bool
    {
        $user = $this->getUserById($id);

        return $user->getPasswordHash()->verify($password);
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->reader->getUsers();
    }
}
