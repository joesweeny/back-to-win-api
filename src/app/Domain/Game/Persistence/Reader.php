<?php

namespace GamePlatform\Domain\Game\Persistence;

use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Uuid\Uuid;

interface Reader
{
    /**
     * Used to retrieve a single Game entity record
     *
     * @param Uuid $gameId
     * @throws NotFoundException
     * @return Game
     */
    public function getById(Uuid $gameId): Game;

    /**
     * Used to retrieve numerous Game entities based on conditions provided on GameRepositoryQuery. If no query
     * is provided then all Game records are returned
     *
     * @param GameRepositoryQuery|null $query
     * @return array|Game[]
     */
    public function get(GameRepositoryQuery $query = null): array;
}
