<?php

namespace BackToWin\Domain\GameResult;

use BackToWin\Domain\GameResult\Persistence\Repository;
use BackToWin\Framework\Exception\RepositoryDuplicationException;
use BackToWin\Framework\Uuid\Uuid;

class GameResultOrchestrator
{
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Uuid $gameId
     * @param Uuid $winnerId
     * @throws RepositoryDuplicationException
     * @return void
     */
    public function saveGameWinner(Uuid $gameId, Uuid $winnerId): void
    {
        $this->repository->insert($gameId, $winnerId);
    }
}
