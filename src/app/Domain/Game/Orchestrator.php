<?php

namespace BackToWin\Domain\Game;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Persistence\GameRepositoryQuery;
use BackToWin\Domain\Game\Persistence\Reader;
use BackToWin\Domain\Game\Persistence\Writer;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;

class Orchestrator
{
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var Writer
     */
    private $writer;

    public function __construct(Reader $reader, Writer $writer)
    {
        $this->reader = $reader;
        $this->writer = $writer;
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
}
