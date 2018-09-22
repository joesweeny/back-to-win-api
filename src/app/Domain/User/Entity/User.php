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
