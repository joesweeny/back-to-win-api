<?php

namespace GamePlatform\Testing\Traits;

use GamePlatform\Domain\Auth\Services\Token\TokenGenerator;
use GamePlatform\Framework\DateTime\Clock;
use GamePlatform\Framework\Uuid\Uuid;
use Interop\Container\ContainerInterface;

trait CreateAuthToken
{
    public function getValidToken(ContainerInterface $container, Uuid $userId = null): string
    {
        $generator = $container->get(TokenGenerator::class);

        $clock = $container->get(Clock::class);

        return $generator->generate(
            $userId ?: Uuid::generate(),
            $clock->now()->addDays(2)
        );
    }
}
