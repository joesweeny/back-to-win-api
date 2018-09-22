<?php

namespace BackToWin\Domain\Admin\Bank\Log;

use BackToWin\Domain\Admin\Bank\Bank;
use BackToWin\Framework\Uuid\Uuid;
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
    public function deposit(Uuid $gameId, Money $money): void
    {
        $this->logger->info("Depositing remaining funds for Game {$gameId}");
    }

    /**
     * @inheritdoc
     */
    public function getBalance(): Money
    {
        return new Money(1000000, new Currency('FAKE'));
    }
}
