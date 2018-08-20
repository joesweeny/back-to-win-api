<?php

namespace GamePlatform\Domain\Admin\Bank;

use GamePlatform\Domain\Admin\Bank\Exception\BankingException;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Money;

interface Bank
{
    /**
     * Deposit funds into Admin bank account
     *
     * @param Uuid $gameId
     * @param Money $money
     * @throws BankingException
     * @return void
     */
    public function deposit(Uuid $gameId, Money $money): void;
}
