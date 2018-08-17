<?php

namespace BackToWin\Domain\Game;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Persistence\GameRepositoryQuery;
use BackToWin\Domain\Game\Persistence\Reader;
use BackToWin\Domain\Game\Persistence\Writer;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\GameEntryManager;
use BackToWin\Domain\GameEntry\GameEntryOrchestrator;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;

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
    /**
     * @var GameEntryManager
     */
    private $entryManager;

    public function __construct(
        Reader $reader,
        Writer $writer,
        GameEntryOrchestrator $entryOrchestrator,
        GameEntryManager $entryManager
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->entryOrchestrator = $entryOrchestrator;
        $this->entryManager = $entryManager;
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

        $this->entryOrchestrator->checkEligibility($game, $user->getId());

        $this->entryManager->handleGameEntryFee($game, $user);

        $this->entryOrchestrator->addGameEntry($game, $user->getId());
    }
}
