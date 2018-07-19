<?php

namespace BackToWin\Domain\Game\Persistence\Illuminate;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Persistence\Hydration\Extractor;
use BackToWin\Domain\Game\Persistence\Writer;
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
    public function insert(Game $game): Game
    {
        $game->setCreatedDate($this->clock->now())
            ->setLastModifiedDate($this->clock->now());

        $this->table()->insert((array) Extractor::toRawData($game));

        return $game;
    }

    /**
     * @inheritdoc
     */
    public function update(Game $game): Game
    {
        if (!$this->table()->where('id', $game->getId()->toBinary())->exists()) {
            throw new NotFoundException("Unable to update Game {$game->getId()} as it does not exist");
        }

        $this->table()->where('id', $game->getId()->toBinary())->update((array) Extractor::toRawData($game));

        return $game;
    }

    private function table(): Builder
    {
        return $this->connection->table('game');
    }
}
