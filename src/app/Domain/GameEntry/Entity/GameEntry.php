<?php

namespace BackToWin\Domain\GameEntry\Entity;

use BackToWin\Framework\Entity\PrivateAttributesTrait;
use BackToWin\Framework\Entity\TimestampedTrait;
use BackToWin\Framework\Uuid\Uuid;

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
