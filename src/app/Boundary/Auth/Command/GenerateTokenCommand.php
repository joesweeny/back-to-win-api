<?php

namespace GamePlatform\Boundary\Auth\Command;

use Chief\Command;
use GamePlatform\Framework\Uuid\Uuid;

class GenerateTokenCommand implements Command
{
    /**
     * @var Uuid
     */
    private $userId;

    public function __construct(Uuid $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}
