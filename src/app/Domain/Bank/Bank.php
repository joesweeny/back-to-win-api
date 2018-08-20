<?php

namespace GamePlatform\Domain\Bank;

use GamePlatform\Domain\Bank\Exception\BankingException;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Money;

interface Bank
{
    /**
     * Create a new User bank account in virtual bank with opening balance
     *
     * @param Uuid $userId
     * @throws BankingException
     * @param Money $money
     */
    public function openAccount(Uuid $userId, Money $money): void;
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
     * @param Money $money
     * @throws BankingException
     * @return Money
     */
    public function withdraw(Uuid $userId, Money $money): Money;

    /**
     * Get the current balance of a Users virtual bank account
     *
     * @param Uuid $userId
     * @throws BankingException
     * @return Money
     */
    public function getBalance(Uuid $userId): Money;
}
