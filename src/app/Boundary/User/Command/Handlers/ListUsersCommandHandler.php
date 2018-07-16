<?php

namespace BackToWin\Boundary\User\Command\Handlers;

use BackToWin\Boundary\User\Command\ListUsersCommand;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Boundary\User\UserPresenter;

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
     * @throws \BackToWin\Framework\Exception\UndefinedException
     */
    public function handle(ListUsersCommand $command): array
    {
        return array_map(function (User $user) {
            return $this->presenter->toDto($user);
        }, $this->orchestrator->getUsers());
    }
}
