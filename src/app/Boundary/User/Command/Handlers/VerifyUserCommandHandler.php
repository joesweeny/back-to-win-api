<?php

namespace BackToWin\Boundary\User\Command\Handlers;

use BackToWin\Boundary\User\Command\VerifyUserCommand;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Framework\Exception\NotAuthenticatedException;
use BackToWin\Framework\Exception\NotFoundException;

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
        $user = $this->orchestrator->verifyUser($command->getEmail(), $command->getPassword());

        return (string) $user->getId();
    }
}
