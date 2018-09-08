<?php

namespace GamePlatform\Boundary\Game\Command\Handlers;

use GamePlatform\Boundary\Game\Command\CreateGameCommand;
use GamePlatform\Boundary\Game\GamePresenter;
use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Exception\GameCreationException;
use GamePlatform\Domain\Game\GameOrchestrator;
use GamePlatform\Framework\Uuid\Uuid;

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
     * @throws GameCreationException
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
