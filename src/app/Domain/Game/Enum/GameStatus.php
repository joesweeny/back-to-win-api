<?php

namespace BackToWin\Domain\Game\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static GameStatus CANCELLED()
 * @method static GameStatus COMPLETED()
 * @method static GameStatus CREATED()
 * @method static GameStatus IN_PLAY()
 */
class GameStatus extends Enum
{
    const CANCELLED = 'CANCELLED';
    const COMPLETED = 'COMPLETED';
    const CREATED = 'CREATED';
    const IN_PLAY = 'IN_PLAY';
}
