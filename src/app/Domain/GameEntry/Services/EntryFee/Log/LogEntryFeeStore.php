<?php

namespace BackToWin\Domain\GameEntry\Services\EntryFee\Log;

use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Services\EntryFee\EntryFeeStore;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use Psr\Log\LoggerInterface;

class LogEntryFeeStore implements EntryFeeStore
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
    public function enter(GameEntry $entry, Money $fee): void
    {
        $this->logger->info("Adding GameEntry fee for User {$entry->getUserId()} into Game {$entry->getGameId()}");
    }

    /**
     * @inheritdoc
     */
    public function getFeeTotal(Uuid $gameId): Money
    {
        return new Money(1000, new Currency('GBP'));
    }

    /**
     * @inheritdoc
     */
    public function delete(Uuid $gameId): void
    {
        $this->logger->info("Deleting record for Game {$gameId}");
    }
}
