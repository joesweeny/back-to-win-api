<?php

namespace GamePlatform\Domain\Auth\Services\Token;

use GamePlatform\Framework\Exception\NotAuthenticatedException;

interface TokenValidator
{
    /**
     * @param string $token
     * @return void
     * @throws NotAuthenticatedException
     */
    public function validate(string $token): void;
}
