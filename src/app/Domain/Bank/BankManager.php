<?php

namespace BackToWin\Domain\Bank;

use BackToWin\Domain\Bank\Exception\BankingException;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Uuid\Uuid;
use Money\Money;

class BankManager
{
    /**
     * @var Bank
     */
    private $bank;

    public function __construct(Bank $bank)
    {
        $this->bank = $bank;
    }

    /**
     * @param Uuid $userId
     * @param Money $money
     * @throws BankingException
     * @return void
     */
    public function openAccount(Uuid $userId, Money $money): void
    {
        $this->bank->openAccount($userId, $money);
    }

    /**
     * Ensure User has enough funds prior to withdrawing money from their account
     *
     * @param User $user
     * @param Money $money
     * @throws BankingException
     * @return Money
     */
    public function withdraw(User $user, Money $money): Money
    {
        $balance = $this->bank->getBalance($user->getId());

        if (!$this->hasSufficientFunds($balance, $money)) {
            throw new BankingException("Cannot withdraw money for User {$user->getId()} due to insufficient funds");
        }

        return $this->bank->withdraw($user->getId(), $money);
    }

    /**
     * @param Uuid $userId
     * @param Money $money
     * @throws BankingException
     * @return void
     */
    public function deposit(Uuid $userId, Money $money)
    {
        $this->bank->deposit($userId, $money);
    }

    /**
     * @param Uuid $userId
     * @return Money
     * @throws BankingException
     */
    public function getBalance(Uuid $userId): Money
    {
        return $this->bank->getBalance($userId);
    }

    /**
     * @param Money $balance
     * @param Money $money
     * @return bool
     * @throws BankingException
     */
    private function hasSufficientFunds(Money $balance, Money $money): bool
    {
        try {
            return $balance->greaterThan($money);
        } catch (\InvalidArgumentException $e) {
            throw new BankingException($e->getMessage());
        }
    }
}
