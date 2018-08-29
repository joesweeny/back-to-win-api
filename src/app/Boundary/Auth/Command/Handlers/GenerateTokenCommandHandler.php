<?php

namespace GamePlatform\Boundary\Auth\Command\Handlers;

use GamePlatform\Bootstrap\Config;
use GamePlatform\Boundary\Auth\Command\GenerateTokenCommand;
use GamePlatform\Domain\Auth\Services\Token\Generator;
use GamePlatform\Framework\DateTime\Clock;

class GenerateTokenCommandHandler
{
    /**
     * @var Generator
     */
    private $generator;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Clock
     */
    private $clock;

    public function __construct(Generator $generator, Clock $clock, Config $config)
    {
        $this->generator = $generator;
        $this->clock = $clock;
        $this->config = $config;
    }

    public function handle(GenerateTokenCommand $command): string
    {
        return $this->generator->generate(
            $command->getUserId(),
            $this->clock->now()->addMinutes($this->config->get('auth.token.expiry'))
        );
    }
}
