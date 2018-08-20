<?php

namespace GamePlatform\Domain\Game;

use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Exception\GameSettlementException;
use GamePlatform\Domain\Game\Persistence\GameRepositoryQuery;
use GamePlatform\Domain\Game\Persistence\Reader;
use GamePlatform\Domain\Game\Persistence\Writer;
use GamePlatform\Domain\GameEntry\Exception\GameEntryException;
use GamePlatform\Domain\GameResult\GameResultOrchestrator;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Uuid\Uuid;

class GameOrchestrator
{
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var Writer
     */
    private $writer;
    /**
     * @var GameResultOrchestrator
     */
    private $resultOrchestrator;

    public function __construct(Reader $reader, Writer $writer, GameResultOrchestrator $resultOrchestrator)
    {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->resultOrchestrator = $resultOrchestrator;
    }

    public function createGame(Game $game): Game
    {
        return $this->writer->insert($game);
    }

    /**
     * @param Uuid $gameId
     * @throws NotFoundException
     * @return Game
     */
    public function getGameById(Uuid $gameId): Game
    {
        return $this->reader->getById($gameId);
    }

    /**
     * @param GameRepositoryQuery|null $query
     * @return array|Game[]
     */
    public function getGames(GameRepositoryQuery $query = null): array
    {
        return $this->reader->get($query);
    }

    /**
     * @param Uuid $gameId
     * @throws GameEntryException
     * @throws \RuntimeException
     * @return Game
     */
    public function getGameToEnter(Uuid $gameId): Game
    {
        $game = $this->reader->getById($gameId);

        if ($game->getStatus()->getValue() !== 'CREATED') {
            throw new GameEntryException("Cannot enter Game {$game->getId()} as game status is {$game->getStatus()}");
        }

        return $game;
    }

    /**
     * @param Uuid $gameId
     * @throws GameSettlementException
     * @throws NotFoundException
     * @return Game
     */
    public function getGameToSettle(Uuid $gameId): Game
    {
        $game = $this->reader->getById($gameId);

        if ($game->getStatus()->getValue() !== 'CREATED') {
            throw new GameSettlementException(
                "Cannot settle Game {$game->getId()} as game status is {$game->getStatus()}"
            );
        }

        return $game;
    }

    public function completeGame(Game $game, Uuid $userId): void
    {
        $this->writer->update($game->setStatus(GameStatus::COMPLETED()));

        $this->resultOrchestrator->saveGameWinner($game->getId(), $userId);
    }
}
