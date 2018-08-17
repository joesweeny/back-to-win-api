<?php

namespace BackToWin\Boundary\Game\Command\Handlers;

use BackToWin\Boundary\Game\Command\CreateGameCommand;
use BackToWin\Boundary\Game\GamePresenter;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\GameOrchestrator;
use BackToWin\Framework\Uuid\Uuid;

class CreateGameCommandHandler
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
            GameStatus::CREATED(),
            $command->getBuyIn(),
            $command->getMax(),
            $command->getMin(),
            $command->getStartDateTime(),
            $command->getPlayers()
        );
    }
}
