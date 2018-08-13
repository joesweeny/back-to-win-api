<?php

namespace BackToWin\Domain\GameEntry\Persistence;

use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;

interface Repository
{
    /**
     * Insert a new GameEntry record into the database
     *
     * @param Uuid $gameId
     * @param Uuid $userId
     * @throws GameEntryException
     * @return GameEntry
     */
    public function insert(Uuid $gameId, Uuid $userId): GameEntry;

    /**
     * Return an array of GameEntry objects for a specific game
     *
     * @param Uuid $gameId
     * @throws NotFoundException
     * @return array|GameEntry[]
     */
    public function get(Uuid $gameId): array;
}
