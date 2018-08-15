<?php

namespace BackToWin\Domain\Admin\Bank\Persistence\Illuminate;

use BackToWin\Domain\Admin\Bank\Persistence\Repository;
use BackToWin\Framework\DateTime\Clock;
use BackToWin\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
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
        $this->connection->table('admin_bank_transaction')->insert([
           'game_id' => $gameId->toBinary(),
           'currency' => $money->getCurrency()->getCode(),
           'amount' => (int) $money->getAmount(),
           'timestamp' => $this->clock->now()->getTimestamp()
        ]);
    }
}
