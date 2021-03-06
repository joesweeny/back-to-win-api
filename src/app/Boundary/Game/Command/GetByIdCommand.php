<?php

namespace BackToWin\Boundary\Game\Command;

use BackToWin\Framework\Uuid\Uuid;
use Chief\Command;

class GetByIdCommand implements Command
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
