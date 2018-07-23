<?php

namespace BackToWin\Boundary\Game\Command\Handlers;

use BackToWin\Boundary\Game\Command\ListGamesCommand;
use BackToWin\Boundary\Game\GamePresenter;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Orchestrator;

class ListGamesCommandHandler
{
    /**
     * @var Orchestrator
     */
    private $orchestrator;
    /**
     * @var GamePresenter
     */
    private $presenter;

    public function __construct(Orchestrator $orchestrator, GamePresenter $presenter)
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
