<?php

namespace GamePlatform\Domain\GameEntry\Entity;

use GamePlatform\Framework\Entity\PrivateAttributesTrait;
use GamePlatform\Framework\Entity\TimestampedTrait;
use GamePlatform\Framework\Uuid\Uuid;

class GameEntry
{
    use PrivateAttributesTrait,
        TimestampedTrait;

    /**
     * @var Uuid
     */
    private $gameId;
    /**
     * @var Uuid
     */
    private $userId;

    public function __construct(Uuid $gameId, Uuid $userId)
    {
        $this->gameId = $gameId;
        $this->userId = $userId;
    }

    public function getGameId(): Uuid
    {
        return $this->gameId;
    }
    
    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}
