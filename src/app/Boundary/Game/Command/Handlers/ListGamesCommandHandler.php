<?php

namespace BackToWin\Boundary\Game\Command\Handlers;

use BackToWin\Boundary\Game\Command\ListGamesCommand;
use BackToWin\Boundary\Game\GamePresenter;
use BackToWin\Boundary\Game\QueryBuilder;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\GameOrchestrator;

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
    /**
     * @var QueryBuilder
     */
    private $builder;

    public function __construct(GameOrchestrator $orchestrator, GamePresenter $presenter, QueryBuilder $builder)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
        $this->builder = $builder;
    }

    /**
     * @param ListGamesCommand $command
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return array
     */
    public function handle(ListGamesCommand $command): array
    {
        $query = $this->builder->buildGameRepositoryQuery($command->getQueryParameters());

        return array_map(function (Game $game) {
            return $this->presenter->toDto($game);
        }, $this->orchestrator->getGames($query));
    }
}
