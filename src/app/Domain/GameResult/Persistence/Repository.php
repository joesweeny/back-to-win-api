<?php

namespace BackToWin\Domain\GameResult\Persistence;

use BackToWin\Framework\Exception\RepositoryDuplicationException;
use BackToWin\Framework\Uuid\Uuid;

interface Repository
{
    /**
     * @param Uuid $gameId
     * @param Uuid $userId
     * @throws RepositoryDuplicationException
     * @return void
     */
    public function insert(Uuid $gameId, Uuid $userId): void;
}
