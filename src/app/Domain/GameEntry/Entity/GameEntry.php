<?php

namespace BackToWin\Domain\GameEntry\Entity;

use BackToWin\Framework\Uuid\Uuid;

class GameEntry
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Uuid
     */
    private $gameId;
    /**
     * @var Uuid
     */
    private $userId;

    public function __construct(int $id, Uuid $gameId, Uuid $userId)
    {
        $this->id = $id;
        $this->gameId = $gameId;
        $this->userId = $userId;
    }

    public function getId(): int
    {
        return $this->id;
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
