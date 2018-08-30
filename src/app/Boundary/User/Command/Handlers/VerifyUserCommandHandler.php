<?php

namespace GamePlatform\Boundary\User\Command\Handlers;

use GamePlatform\Boundary\User\Command\VerifyUserCommand;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Framework\Exception\NotAuthenticatedException;
use GamePlatform\Framework\Exception\NotFoundException;

class VerifyUserCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;

    public function __construct(UserOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param VerifyUserCommand $command
     * @return string
     * @throws NotAuthenticatedException
     * @throws NotFoundException
     */
    public function handle(VerifyUserCommand $command): string
    {
        return (string) $this->orchestrator->verifyUser($command->getEmail(), $command->getPassword());
    }
}
