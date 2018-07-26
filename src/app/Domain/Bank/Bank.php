<?php

namespace BackToWin\Domain\Bank;

use BackToWin\Domain\Bank\Exception\BankingException;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;

interface Bank
{
    /**
     * Create a new User bank account reckon in the chosen currency
     *
     * @param Uuid $userId
     * @throws BankingException
     * @param Currency $currency
     */
    public function openAccount(Uuid $userId, Currency $currency): void;
    /**
     * Add money to a Users virtual bank account
     *
     * @param Uuid $userId
     * @throws BankingException
     * @param Money $money
     */
    public function deposit(Uuid $userId, Money $money): void;

    /**
     * Subtract money from a Users virtual bank account
     *
     * @param Uuid $userId
     * @throws BankingException
     * @param Money $money
     */
    public function withdraw(Uuid $userId, Money $money): void;

    /**
     * Get the current balance of a Users virtual bank account
     *
     * @param Uuid $userId
     * @throws BankingException
     * @return Money
     */
    public function getBalance(Uuid $userId): Money;
}
