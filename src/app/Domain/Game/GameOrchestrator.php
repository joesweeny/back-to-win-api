<?php

namespace BackToWin\Domain\Game;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Exception\GameSettlementException;
use BackToWin\Domain\Game\Persistence\GameRepositoryQuery;
use BackToWin\Domain\Game\Persistence\Reader;
use BackToWin\Domain\Game\Persistence\Writer;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\GameEntryOrchestrator;
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
     * @var GameEntryOrchestrator
     */
    private $entryOrchestrator;

    public function __construct(
        Reader $reader,
        Writer $writer,
        GameEntryOrchestrator $entryOrchestrator
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->entryOrchestrator = $entryOrchestrator;
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

        $this->entryOrchestrator->addGameEntry($game, $user);
    }

    public function settleGame(Uuid $gameId, User $user, Money $winnings)
    {
        $game = $this->reader->getById($gameId);

        // Check User was in Game
        if ($this->entryOrchestrator->isUserInGame($game, $user->getId())) {
            throw new GameSettlementException("Unable to settle as User {$user->getId()} did not enter Game {$gameId}");
        }

        // GameSettler class to:
        // - Get total from entry fee pot
        // - Divide money
        // - Put winnings in User bank
        // - Add UserPurseTransaction and update UserPurse
        // - Update Admin funds/transaction
        // - Delete EntryFeeStore record

        // Set GameStatus to COMPLETED

        // Add GameResult record
    }
}
