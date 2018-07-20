<?php

namespace BackToWin\Boundary\Game\Command\Handlers;

use BackToWin\Boundary\Game\Command\CreateGameCommand;
use BackToWin\Boundary\Game\GamePresenter;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Orchestrator;
use BackToWin\Framework\Uuid\Uuid;

class CreateGameCommandHandler
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

    /**
     * @param CreateGameCommand $command
     * @return \stdClass
     */
    public function handle(CreateGameCommand $command): \stdClass
    {
        $game = $this->orchestrator->createGame($this->hydrateGameObject($command));

        return $this->presenter->toDto($game);
    }

    private function hydrateGameObject(CreateGameCommand $command): Game
    {
        return new Game(
            Uuid::generate(),
            $command->getType(),
            $command->getStatus(),
            $command->getMax(),
            $command->getMin(),
            $command->getStartDateTime(),
            $command->getPlayers()
        );
    }
}
