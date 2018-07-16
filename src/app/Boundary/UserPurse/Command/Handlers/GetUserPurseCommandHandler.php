<?php

namespace BackToWin\Boundary\UserPurse\Command\Handlers;

use BackToWin\Boundary\UserPurse\Command\GetUserPurseCommand;
use BackToWin\Boundary\UserPurse\UserPursePresenter;
use BackToWin\Domain\UserPurse\Orchestrator;
use BackToWin\Framework\Exception\NotFoundException;

class GetUserPurseCommandHandler
{
    /**
     * @var Orchestrator
     */
    private $orchestrator;
    /**
     * @var UserPursePresenter
     */
    private $presenter;

    public function __construct(Orchestrator $orchestrator, UserPursePresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetUserPurseCommand $command
     * @throws NotFoundException
     * @return \stdClass
     */
    public function handle(GetUserPurseCommand $command): \stdClass
    {
        return $this->presenter->toDto($this->orchestrator->getUserPurse($command->getUserId()));
    }
}
