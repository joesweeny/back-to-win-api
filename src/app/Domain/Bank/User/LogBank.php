<?php

namespace GamePlatform\Domain\Bank\User;

use GamePlatform\Domain\Bank\Bank;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use Psr\Log\LoggerInterface;

class LogBank implements Bank
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function openAccount(Uuid $userId, Money $money): void
    {
        $this->logger->info("Account opened for User {$userId}");
    }

    /**
     * @inheritdoc
     */
    public function deposit(Uuid $userId, Money $money): void
    {
        $this->logger->info("Funds deposited for User {$userId}");
    }

    /**
     * @inheritdoc
     */
    public function withdraw(Uuid $userId, Money $money): Money
    {
        $this->logger->info("Funds withdrawn for User {$userId}");

        return $money;
    }

    /**
     * @inheritdoc
     */
    public function getBalance(Uuid $userId): Money
    {
        $this->logger->info("Returning balance for User {$userId}");

        return new Money(1000000000, new Currency('GBP'));
    }
}
