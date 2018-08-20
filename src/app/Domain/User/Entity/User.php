<?php

namespace GamePlatform\Domain\User\Entity;

use GamePlatform\Framework\Entity\PrivateAttributesTrait;
use GamePlatform\Framework\Entity\TimestampedTrait;
use GamePlatform\Framework\Identity\IdentifiedByUuidTrait;
use GamePlatform\Framework\Password\PasswordHash;

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
     * @throws \GamePlatform\Framework\Exception\UndefinedException
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
     * @throws \GamePlatform\Framework\Exception\UndefinedException
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
     * @throws \GamePlatform\Framework\Exception\UndefinedException
     */
    public function getPasswordHash(): PasswordHash
    {
        return $this->getOrFail('password');
    }
}
