<?php

namespace GamePlatform\Domain\Game\Persistence;

use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Framework\Exception\NotFoundException;

interface Writer
{
    /**
     * Insert a new Game record in the database
     *
     * @param Game $game
     * @return Game
     */
    public function insert(Game $game): Game;

    /**
     * Update an existing Game record in the database
     *
     * @param Game $game
     * @throws NotFoundException
     * @return Game
     */
    public function update(Game $game): Game;
}
