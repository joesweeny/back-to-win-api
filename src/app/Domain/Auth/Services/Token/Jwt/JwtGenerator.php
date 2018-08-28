<?php

namespace GamePlatform\Domain\Auth\Services\Token\Jwt;

use GamePlatform\Domain\Auth\Services\Token\Generator;
use GamePlatform\Framework\Uuid\Uuid;

class JwtGenerator implements Generator
{
    /**
     * @inheritdoc
     */
    public function generate(Uuid $userId, \DateTimeImmutable $expiry): string
    {
        // TODO: Implement generate() method.
    }
}
