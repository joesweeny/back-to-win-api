<?php

namespace BackToWin\Domain\Game\Persistence\Illuminate;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Persistence\GameRepositoryQuery;
use BackToWin\Domain\Game\Persistence\Hydration\Hydrator;
use BackToWin\Domain\Game\Persistence\Reader;
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
    /**
     * @var GameQueryBuilder
     */
    private $builder;

    public function __construct(Connection $connection, GameQueryBuilder $builder)
    {
        $this->connection = $connection;
        $this->builder = $builder;
    }

    /**
     * @inheritdoc
     */
    public function getById(Uuid $gameId): Game
    {
        if (!$row = $this->table()->where('id', $gameId->toBinary())->first()) {
            throw new NotFoundException("Unable to retrieve Game {$gameId} as it does not exist");
        }

        return Hydrator::fromRawData($row);
    }

    /**
     * @inheritdoc
     */
    public function get(GameRepositoryQuery $query = null): array
    {
        return array_map(function (\stdClass $row) {
            return Hydrator::fromRawData($row);
        }, $this->builder->build($this->table(), $query)->get()->toArray());
    }

    private function table(): Builder
    {
        return $this->connection->table('game');
    }
}
