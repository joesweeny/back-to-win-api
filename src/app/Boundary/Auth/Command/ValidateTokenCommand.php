<?php

namespace BackToWin\Boundary\Auth\Command;

use Chief\Command;

class ValidateTokenCommand implements Command
{
    /**
     * @var string
     */
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
