<?php

namespace BackToWin\Boundary\User\Command;

use BackToWin\Framework\CommandBus\Command;
use BackToWin\Framework\Password\PasswordHash;
use Money\Currency;

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
    /**
     * @var Currency
     */
    private $currency;

    public function __construct(
        string $username,
        string $email,
        string $password,
        string $currency
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->currency = new Currency($currency);
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

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
