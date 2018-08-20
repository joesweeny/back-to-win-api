<?php

namespace GamePlatform\Domain\GameResult\Persistence\Illuminate;

use GamePlatform\Domain\GameResult\Persistence\Repository;
use GamePlatform\Framework\DateTime\Clock;
use GamePlatform\Framework\Exception\RepositoryDuplicationException;
use GamePlatform\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

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
    public function insert(Uuid $gameId, Uuid $userId): void
    {
        if ($this->table()->where('game_id', $gameId->toBinary())->exists()) {
            throw new RepositoryDuplicationException("Game result for Game {$gameId} already exists");
        }

        $this->table()->insert([
            'game_id' => $gameId->toBinary(),
            'winner_id' => $userId->toBinary(),
            'timestamp' => $this->clock->now()->getTimestamp()
        ]);
    }

    private function table(): Builder
    {
        return $this->connection->table('game_result');
    }
}
