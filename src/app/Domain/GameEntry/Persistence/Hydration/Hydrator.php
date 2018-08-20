<?php

namespace GamePlatform\Domain\GameEntry\Persistence\Hydration;

use GamePlatform\Domain\GameEntry\Entity\GameEntry;
use GamePlatform\Framework\Uuid\Uuid;

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
