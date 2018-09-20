<?php

namespace GamePlatform\Boundary\Avatar\Command;

use Chief\Command;
use GamePlatform\Framework\Uuid\Uuid;

class GetAvatarCommand implements Command
{
    /**
     * @var Uuid
     */
    private $userId;

    public function __construct(string $userId)
    {
        $this->userId = new Uuid($userId);
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}
