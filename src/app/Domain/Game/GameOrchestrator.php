<?php

namespace BackToWin\Domain\Game;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Exception\GameSettlementException;
use BackToWin\Domain\Game\Persistence\GameRepositoryQuery;
use BackToWin\Domain\Game\Persistence\Reader;
use BackToWin\Domain\Game\Persistence\Writer;
use BackToWin\Domain\Game\Services\GameKeeper;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameResult\GameResultOrchestrator;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;
use Money\Money;

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
    /**
     * @var GameKeeper
     */
    private $keeper;

    public function __construct(
        Reader $reader,
        Writer $writer,
        GameKeeper $keeper,
        GameResultOrchestrator $resultOrchestrator
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->resultOrchestrator = $resultOrchestrator;
        $this->keeper = $keeper;
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
     * @param User $user
     * @throws GameEntryException
     * @throws \RuntimeException
     * @return void
     */
    public function addUserToGame(Uuid $gameId, User $user): void
    {
        $game = $this->reader->getById($gameId);

        if ($game->getStatus()->getValue() !== 'CREATED') {
            throw new GameEntryException("Cannot enter Game {$game->getId()} as game status is {$game->getStatus()}");
        }

        $this->keeper->processUserGameEntry($game, $user);
    }

    /**
     * @param Uuid $gameId
     * @param User $user
     * @param Money $winningTotal
     * @throws GameSettlementException
     * @return void
     */
    public function settleGame(Uuid $gameId, User $user, Money $winningTotal): void
    {
        $game = $this->reader->getById($gameId);

        if ($game->getStatus()->getValue() !== 'CREATED') {
            throw new GameSettlementException(
                "Cannot settle Game {$game->getId()} as game status is {$game->getStatus()}"
            );
        }

        $this->keeper->processGameSettlement($game, $user, $winningTotal);

        $this->writer->update($game->setStatus(GameStatus::COMPLETED()));

        $this->resultOrchestrator->saveGameWinner($gameId, $user->getId());
    }
}
