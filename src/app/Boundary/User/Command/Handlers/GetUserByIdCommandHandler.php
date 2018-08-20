<?php

namespace GamePlatform\Boundary\User\Command\Handlers;

use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Boundary\User\Command\GetUserByIdCommand;
use GamePlatform\Boundary\User\UserPresenter;

class GetUserByIdCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;
    /**
     * @var UserPresenter
     */
    private $presenter;

    /**
     * GetUserByIdCommandHandler constructor.
     * @param UserOrchestrator $orchestrator
     * @param UserPresenter $presenter
     */
    public function __construct(UserOrchestrator $orchestrator, UserPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetUserByIdCommand $command
     * @return \stdClass
     * @throws \GamePlatform\Framework\Exception\NotFoundException
     */
    public function handle(GetUserByIdCommand $command): \stdClass
    {
        return $this->presenter->toDto($this->orchestrator->getUserById($command->getId()));
    }
}
