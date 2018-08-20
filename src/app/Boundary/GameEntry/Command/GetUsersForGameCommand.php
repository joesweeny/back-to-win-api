<?php

namespace GamePlatform\Boundary\GameEntry\Command;

use GamePlatform\Framework\Uuid\Uuid;
use Chief\Command;

class GetUsersForGameCommand implements Command
{
    /**
     * @var Uuid
     */
    private $gameId;

    public function __construct(string $gameId)
    {
        $this->gameId = new Uuid($gameId);
    }

    public function getGameId(): Uuid
    {
        return $this->gameId;
    }
}
