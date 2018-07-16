<?php

namespace BackToWin\Domain\UserPurse\Persistence\Illuminate;

use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Domain\UserPurse\Persistence\Hydration\Extractor;
use BackToWin\Domain\UserPurse\Persistence\Writer;
use BackToWin\Framework\DateTime\Clock;
use BackToWin\Framework\Exception\NotFoundException;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

class IlluminateWriter implements Writer
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var Clock
     */
    private $clock;

    public function __construct(Connection $connection, Clock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    /**
     * @inheritdoc
     */
    public function insert(UserPurse $purse): void
    {
        $purse->setCreatedDate($this->clock->now())
            ->setLastModifiedDate($this->clock->now());

        $this->purseTable()->insert((array) Extractor::purseToRawData($purse));
    }

    /**
     * @inheritdoc
     */
    public function update(UserPurse $purse): void
    {
        if (!$this->purseTable()->where('user_id', $purse->getUserId()->toBinary())->exists()) {
            throw new NotFoundException("Purse for User {$purse->getUserId()} does not exist");
        }

        $purse->setLastModifiedDate($this->clock->now());

        $this->purseTable()
            ->where('user_id', $purse->getUserId()->toBinary())
            ->update((array) Extractor::purseToRawData($purse));
    }

    /**
     * @inheritdoc
     */
    public function insertTransaction(UserPurseTransaction $transaction): void
    {
        $transaction->setCreatedDate($this->clock->now());

        $this->transactionTable()->insert((array) Extractor::transactionToRawData($transaction));
    }

    private function purseTable(): Builder
    {
        return $this->connection->table('user_purse');
    }

    private function transactionTable(): Builder
    {
        return $this->connection->table('user_purse_transaction');
    }
}
