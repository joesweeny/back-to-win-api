<?php

namespace BackToWin\Domain\Admin\Bank\Persistence;

use BackToWin\Framework\Uuid\Uuid;
use Money\Money;

interface Repository
{
    /**
     * Add a transaction record to the database
     *
     * @param Uuid $gameId
     * @param Money $money
     */
    public function insert(Uuid $gameId, Money $money): void;
}
