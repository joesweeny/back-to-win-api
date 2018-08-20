<?php

namespace GamePlatform\Boundary\User\Command;

use GamePlatform\Framework\CommandBus\Command;
use GamePlatform\Framework\Password\PasswordHash;

class CreateUserCommand implements Command
{
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $password;

    public function __construct(
        string $username,
        string $email,
        string $password
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): PasswordHash
    {
        return PasswordHash::createFromRaw($this->password);
    }
}
