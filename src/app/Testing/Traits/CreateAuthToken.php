<?php

namespace BackToWin\Testing\Traits;

use BackToWin\Domain\Auth\Services\Token\TokenGenerator;
use BackToWin\Framework\DateTime\Clock;
use BackToWin\Framework\Uuid\Uuid;
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
