<?php

namespace GamePlatform\Domain\Admin\Bank\Persistence;

use GamePlatform\Framework\Exception\RepositoryDuplicationException;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Money;

interface Repository
{
    /**
     * Add a transaction record to the database
     *
     * @param Uuid $gameId
     * @param Money $money
     * @throws RepositoryDuplicationException
     * @return void
     */
    public function insert(Uuid $gameId, Money $money): void;

    /**
     * @param Uuid $gameId
     * @return void
     */
    public function delete(Uuid $gameId): void;
}
