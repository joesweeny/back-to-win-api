<?php

namespace GamePlatform\Boundary\Game\Command\Handlers;

use GamePlatform\Boundary\Game\Command\ListGamesCommand;
use GamePlatform\Boundary\Game\GamePresenter;
use GamePlatform\Boundary\Game\QueryBuilder;
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
