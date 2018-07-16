<?php

namespace BackToWin\Domain\User;

use BackToWin\Domain\User\Persistence\Reader;
use BackToWin\Domain\User\Persistence\Writer;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Exception\UserCreationException;
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
     * @throws UserCreationException
     * @return User
     */
    public function createUser(User $user): User
    {
        if ($this->userExistsWithEmail($user)) {
            throw new UserCreationException("A user has already registered with this email address {$user->getEmail()}");
        }

        if ($this->userExistsWithUsername($user)) {
            throw new UserCreationException("A user has already registered with this username {$user->getUsername()}");
        }

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

    public function userExistsWithEmail(User $user): bool
    {
        try {
            $this->getUserByEmail($user->getEmail());
            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }

    public function userExistsWithUsername(User $user): bool
    {
        try {
            $this->reader->getByUsername($user->getUsername());
            return true;
        } catch (NotFoundException $e) {
            return false;
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
