<?php

namespace GamePlatform\Boundary\UserPurse\Command\Handlers;

use GamePlatform\Boundary\UserPurse\Command\GetUserPurseCommand;
use GamePlatform\Boundary\UserPurse\UserPursePresenter;
use GamePlatform\Domain\UserPurse\UserPurseOrchestrator;
use GamePlatform\Framework\Exception\NotFoundException;

class GetUserPurseCommandHandler
{
    /**
     * @var UserPurseOrchestrator
     */
    private $orchestrator;
    /**
     * @var UserPursePresenter
     */
    private $presenter;

    public function __construct(UserPurseOrchestrator $orchestrator, UserPursePresenter $presenter)
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
