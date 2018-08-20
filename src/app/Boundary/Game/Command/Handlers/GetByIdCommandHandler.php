<?php

namespace GamePlatform\Boundary\Game\Command\Handlers;

use GamePlatform\Boundary\Game\Command\GetByIdCommand;
use GamePlatform\Boundary\Game\GamePresenter;
use GamePlatform\Domain\Game\GameOrchestrator;
use GamePlatform\Framework\Exception\NotFoundException;

class GetByIdCommandHandler
{
    /**
     * @var GameOrchestrator
     */
    private $orchestrator;
    /**
     * @var GamePresenter
     */
    private $presenter;

    public function __construct(GameOrchestrator $orchestrator, GamePresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetByIdCommand $command
     * @throws NotFoundException
     * @return \stdClass
     */
    public function handle(GetByIdCommand $command): \stdClass
    {
        $game = $this->orchestrator->getGameById($command->getGameId());

        return $this->presenter->toDto($game);
    }
}
