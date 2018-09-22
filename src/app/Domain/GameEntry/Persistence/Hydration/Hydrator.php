<?php

namespace BackToWin\Domain\GameEntry\Persistence\Hydration;

use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Framework\Uuid\Uuid;

class Hydrator
{
    /**
     * Hydrate a GameEntry object from scalar object
     *
     * @param \stdClass $data
     * @return GameEntry
     */
    public static function fromRawData(\stdClass $data): GameEntry
    {
        return (new GameEntry(
            Uuid::createFromBinary($data->game_id),
            Uuid::createFromBinary($data->user_id)
        ))->setCreatedDate((new \DateTimeImmutable())->setTimestamp($data->timestamp));
    }
}
