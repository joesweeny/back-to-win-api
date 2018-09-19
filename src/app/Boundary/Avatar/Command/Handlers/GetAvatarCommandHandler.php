<?php

namespace GamePlatform\Boundary\Avatar\Command\Handlers;

use GamePlatform\Boundary\Avatar\AvatarPresenter;
use GamePlatform\Boundary\Avatar\Command\GetAvatarCommand;
use GamePlatform\Domain\Avatar\AvatarOrchestrator;
use GamePlatform\Framework\Exception\NotFoundException;

class GetAvatarCommandHandler
{
    /**
     * @var AvatarOrchestrator
     */
    private $orchestrator;
    /**
     * @var AvatarPresenter
     */
    private $presenter;

    public function __construct(AvatarOrchestrator $orchestrator, AvatarPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetAvatarCommand $command
     * @return \stdClass
     * @throws NotFoundException
     */
    public function handle(GetAvatarCommand $command): \stdClass
    {
        $avatar = $this->orchestrator->getAvatar($command->getUserId());

        return $this->presenter->toDto($avatar);
    }
}
