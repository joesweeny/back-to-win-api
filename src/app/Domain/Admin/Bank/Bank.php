<?php

namespace BackToWin\Domain\Admin\Bank;

use BackToWin\Domain\Admin\Bank\Exception\BankingException;
use BackToWin\Framework\Uuid\Uuid;
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

    /**
     * Get total balance of all funds deposited into account
     *
     * @return Money
     */
    public function getBalance(): Money;
}
