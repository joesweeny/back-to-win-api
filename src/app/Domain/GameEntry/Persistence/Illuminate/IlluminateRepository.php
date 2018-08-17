<?php

namespace BackToWin\Domain\GameEntry\Persistence\Illuminate;

use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\Persistence\Hydration\Hydrator;
use BackToWin\Domain\GameEntry\Persistence\Repository;
use BackToWin\Framework\DateTime\Clock;
use BackToWin\Framework\Exception\RepositoryDuplicationException;
use BackToWin\Framework\Uuid\Uuid;
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
    public function insert(Uuid $gameId, Uuid $userId): GameEntry
    {
        if ($this->exists($gameId, $userId)) {
            throw new RepositoryDuplicationException("User {$userId} has already entered game {$gameId}");
        }

        $this->table()->insert($data = [
            'game_id' => $gameId->toBinary(),
            'user_id' => $userId->toBinary(),
            'timestamp' => $this->clock->now()->getTimestamp()
        ]);

        return Hydrator::fromRawData((object) $data);
    }

    /**
     * @inheritdoc
     */
    public function get(Uuid $gameId): array
    {
        return array_map(function (\stdClass $row) {
            return Hydrator::fromRawData($row);
        }, $this->table()->where('game_id', $gameId->toBinary())->get()->toArray());
    }

    /**
     * @inheritdoc
     */
    public function exists(Uuid $gameId, Uuid $userId): bool
    {
        return $this->table()->where('game_id', $gameId->toBinary())->where('user_id', $userId->toBinary())->exists();
    }

    private function table(): Builder
    {
        return $this->connection->table('game_entry');
    }
}
