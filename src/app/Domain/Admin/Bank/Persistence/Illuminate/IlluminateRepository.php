<?php

namespace BackToWin\Domain\Admin\Bank\Persistence\Illuminate;

use BackToWin\Framework\Exception\RepositoryDuplicationException;
use BackToWin\Domain\Admin\Bank\Persistence\Repository;
use BackToWin\Framework\DateTime\Clock;
use BackToWin\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Money\Money;

class IlluminateRepository implements Repository
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
    public function insert(Uuid $gameId, Money $money): void
    {
        if ($this->whereGameIdEquals($gameId)->exists()) {
            throw new RepositoryDuplicationException("Record for Game {$gameId} already exists");
        }

        $this->table()->insert([
           'game_id' => $gameId->toBinary(),
           'currency' => $money->getCurrency()->getCode(),
           'amount' => (int) $money->getAmount(),
           'timestamp' => $this->clock->now()->getTimestamp()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function delete(Uuid $gameId): void
    {
        $this->whereGameIdEquals($gameId)->delete();
    }

    private function table(): Builder
    {
        return $this->connection->table('admin_bank_transaction');
    }

    private function whereGameIdEquals(Uuid $gameId): Builder
    {
        return $this->table()->where('game_id', $gameId->toBinary());
    }
}
