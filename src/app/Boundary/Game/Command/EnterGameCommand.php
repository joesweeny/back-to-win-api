<?php

namespace BackToWin\Boundary\Game\Command;

use BackToWin\Framework\Uuid\Uuid;
use Chief\Command;

class EnterGameCommand implements Command
{
    /**
     * @var Uuid
     */
    private $gameId;
    /**
     * @var Uuid
     */
    private $userId;

    public function __construct(string $gameId, string $userId)
    {
        $this->gameId = new Uuid($gameId);
        $this->userId = new Uuid($userId);
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
