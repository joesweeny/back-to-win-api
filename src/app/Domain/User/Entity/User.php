<?php

namespace BackToWin\Domain\User\Entity;

use BackToWin\Framework\Entity\PrivateAttributesTrait;
use BackToWin\Framework\Entity\TimestampedTrait;
use BackToWin\Framework\Identity\IdentifiedByUuidTrait;
use BackToWin\Framework\Password\PasswordHash;

class User
{
    use IdentifiedByUuidTrait;
    use PrivateAttributesTrait;
    use TimestampedTrait;

    public function setUsername(string $username): User
    {
        return $this->set('username', $username);
    }

    /**
     * @return string
     * @throws \BackToWin\Framework\Exception\UndefinedException
     */
    public function getUsername(): string
    {
        return $this->get('username');
    }

    public function setFirstName(string $username): User
    {
        return $this->set('first_name', $username);
    }

    /**
     * @return string
     * @throws \BackToWin\Framework\Exception\UndefinedException
     */
    public function getFirstName(): string
    {
        return $this->get('first_name');
    }

    public function setLastName(string $username): User
    {
        return $this->set('last_name', $username);
    }

    /**
     * @return string
     * @throws \BackToWin\Framework\Exception\UndefinedException
     */
    public function getLastName(): string
    {
        return $this->get('last_name');
    }

    public function setEmail(string $email): User
    {
        return $this->set('email', $email);
    }

    /**
     * @return string
     * @throws \BackToWin\Framework\Exception\UndefinedException
     */
    public function getEmail(): string
    {
        return $this->getOrFail('email');
    }

    public function setLocation(string $username): User
    {
        return $this->set('location', $username);
    }

    /**
     * @return string
     * @throws \BackToWin\Framework\Exception\UndefinedException
     */
    public function getLocation(): string
    {
        return $this->get('location');
    }

    public function setPasswordHash(PasswordHash $password): User
    {
        return $this->set('password', $password);
    }

    /**
     * @return PasswordHash
     * @throws \BackToWin\Framework\Exception\UndefinedException
     */
    public function getPasswordHash(): PasswordHash
    {
        return $this->getOrFail('password');
    }
}
