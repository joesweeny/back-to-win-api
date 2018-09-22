<?php

namespace BackToWin\Domain\Auth\Services\Token;

use BackToWin\Framework\Exception\NotAuthenticatedException;

interface TokenValidator
{
    /**
     * @param string $token
     * @return void
     * @throws NotAuthenticatedException
     */
    public function validate(string $token): void;
}
