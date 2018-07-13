<?php

namespace BackToWin\Boundary\User\Command;

use BackToWin\Framework\CommandBus\Command;
use BackToWin\Framework\Password\PasswordHash;

class CreateUserCommand implements Command
{
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $location;

    public function __construct(
        string $username,
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $location
    ) {
        $this->username = $username;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->location = $location;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getLocation(): string
    {
        return $this->location;
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
