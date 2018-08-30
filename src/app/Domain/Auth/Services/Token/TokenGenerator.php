<?php

namespace GamePlatform\Domain\Auth\Services\Token;

use GamePlatform\Framework\Uuid\Uuid;

interface TokenGenerator
{
    /**
     * @param Uuid $userId
     * @param \DateTimeImmutable $expiry
     * @return string
     */
    public function generate(Uuid $userId, \DateTimeImmutable $expiry): string;
}
