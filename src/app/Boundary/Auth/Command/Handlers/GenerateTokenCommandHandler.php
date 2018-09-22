<?php

namespace BackToWin\Boundary\Auth\Command\Handlers;

use BackToWin\Boundary\Auth\Command\GenerateTokenCommand;
use BackToWin\Domain\Auth\Services\Token\TokenOrchestrator;
use BackToWin\Framework\Exception\NotFoundException;

class GenerateTokenCommandHandler
{
    /**
     * @var TokenOrchestrator
     */
    private $orchestrator;

    public function __construct(TokenOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param GenerateTokenCommand $command
     * @return string
     * @throws NotFoundException
     */
    public function handle(GenerateTokenCommand $command): string
    {
        return $this->orchestrator->createNewToken($command->getUserId());
    }
}
