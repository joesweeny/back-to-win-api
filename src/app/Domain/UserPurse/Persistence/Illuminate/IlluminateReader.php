<?php

namespace BackToWin\Domain\UserPurse\Persistence\Illuminate;

use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Domain\UserPurse\Persistence\Hydration\Hydrator;
use BackToWin\Domain\UserPurse\Persistence\Reader;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

class IlluminateReader implements Reader
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritdoc
     */
    public function getPurse(Uuid $userId): UserPurse
    {
        if (!$row = $this->purseTable()->where('user_id', $userId->toBinary())->first()) {
            throw new NotFoundException("Purse for User {$userId} does not exist");
        }

        return Hydrator::hydratePurse($row);
    }

    /**
     * @inheritdoc
     */
    public function getTransaction(Uuid $transactionId): UserPurseTransaction
    {
        if (!$row = $this->transactionTable()->where('id', $transactionId->toBinary())->first()) {
            throw new NotFoundException("Transaction with ID {$transactionId} does not exist");
        }

        return Hydrator::hydrateTransaction($row);
    }

    /**
     * @inheritdoc
     */
    public function getTransactionsForUser(Uuid $userId): array
    {
        return array_map(function (\stdClass $row) {
            return Hydrator::hydrateTransaction($row);
        }, $this->transactionTable()->where('user_id', $userId->toBinary())->get()->toArray());
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
