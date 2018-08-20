<?php

namespace GamePlatform\Boundary\Game\Command\Handlers;

use GamePlatform\Boundary\Game\Command\ListGamesCommand;
use GamePlatform\Boundary\Game\GamePresenter;
use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\GameOrchestrator;

class ListGamesCommandHandler
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

    public function handle(ListGamesCommand $command): array
    {
        return array_map(function (Game $game) {
            return $this->presenter->toDto($game);
        }, $this->orchestrator->getGames());
    }
}
