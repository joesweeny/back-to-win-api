<?php

namespace GamePlatform\Domain\GameEntry\Persistence;

use GamePlatform\Domain\GameEntry\Entity\GameEntry;
use GamePlatform\Domain\GameEntry\Exception\GameEntryException;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Exception\RepositoryDuplicationException;
use GamePlatform\Framework\Uuid\Uuid;

interface Repository
{
    /**
     * Insert a new GameEntry record into the database
     *
     * @param Uuid $gameId
     * @param Uuid $userId
     * @throws RepositoryDuplicationException
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

    /**
     * Confirm that a User/Game record exists in the database
     *
     * @param Uuid $gameId
     * @param Uuid $userId
     * @return bool
     */
    public function exists(Uuid $gameId, Uuid $userId): bool;
}
