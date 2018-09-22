<?php

namespace BackToWin\Domain\Auth\Services\Token;

use BackToWin\Framework\Uuid\Uuid;

interface TokenGenerator
{
    /**
     * @param Uuid $userId
     * @param \DateTimeImmutable $expiry
     * @return string
     */
    public function generate(Uuid $userId, \DateTimeImmutable $expiry): string;
}
