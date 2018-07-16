<?php

namespace BackToWin\Boundary\User\Command\Handlers;

use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Boundary\User\Command\GetUserByIdCommand;
use BackToWin\Boundary\User\UserPresenter;

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
     * @throws \BackToWin\Framework\Exception\NotFoundException
     */
    public function handle(GetUserByIdCommand $command): \stdClass
    {
        return $this->presenter->toDto($this->orchestrator->getUserById($command->getId()));
    }
}
