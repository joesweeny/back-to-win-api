<?php

namespace BackToWin\Domain\Game\Persistence;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Framework\Exception\NotFoundException;

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
