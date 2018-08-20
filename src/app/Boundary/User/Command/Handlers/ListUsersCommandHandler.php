<?php

namespace GamePlatform\Boundary\User\Command\Handlers;

use GamePlatform\Boundary\User\Command\ListUsersCommand;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Boundary\User\UserPresenter;

class ListUsersCommandHandler
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
     * ListUsersCommandHandler constructor.
     * @param UserOrchestrator $orchestrator
     * @param UserPresenter $presenter
     */
    public function __construct(UserOrchestrator $orchestrator, UserPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param ListUsersCommand $command
     * @return array
     * @throws \GamePlatform\Framework\Exception\UndefinedException
     */
    public function handle(ListUsersCommand $command): array
    {
        return array_map(function (User $user) {
            return $this->presenter->toDto($user);
        }, $this->orchestrator->getUsers());
    }
}
