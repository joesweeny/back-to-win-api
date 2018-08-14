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

    private function hasSufficientFunds(Money $balance, Money $money): bool
    {
        // Throw BankException if currencies are not the same
        return $balance->greaterThan($money);
    }
}
