<?php

namespace GamePlatform\Domain\GameResult\Persistence;

use GamePlatform\Framework\Exception\RepositoryDuplicationException;
use GamePlatform\Framework\Uuid\Uuid;

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
