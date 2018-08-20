<?php

namespace GamePlatform\Domain\Admin\Bank\Log;

use GamePlatform\Domain\Admin\Bank\Bank;
use GamePlatform\Framework\Uuid\Uuid;
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
}