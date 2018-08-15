<?php

namespace BackToWin\Domain\Admin\Bank;

use BackToWin\Framework\Uuid\Uuid;
use Money\Money;

interface Bank
{
    /**
     * Deposit funds into Admin bank account
     *
     * @param Uuid $gameId
     * @param Money $money
     */
    public function deposit(Uuid $gameId, Money $money): void;
}
