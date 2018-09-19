<?php

namespace GamePlatform\Boundary\Avatar\Command\Handlers;

use GamePlatform\Boundary\Avatar\Command\AddAvatarCommand;
use GamePlatform\Domain\Avatar\AvatarOrchestrator;
use GamePlatform\Domain\Avatar\Entity\Avatar;

class AddAvatarCommandHandler
{
    /**
     * @var AvatarOrchestrator
     */
    private $orchestrator;

    public function __construct(AvatarOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param AddAvatarCommand $command
     * @return bool
     */
    public function handle(AddAvatarCommand $command): bool
    {
        return $this->orchestrator->addAvatar($this->hydrateAvatar($command));
    }

    private function hydrateAvatar(AddAvatarCommand $command): Avatar
    {
        $avatar = new Avatar($command->getUserId(), $command->getFilename());

        return $avatar->setFileContents($command->getContents());
    }
}
