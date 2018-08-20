<?php

namespace GamePlatform\Boundary\UserPurse\Command;

use GamePlatform\Framework\Uuid\Uuid;
use Chief\Command;

class GetUserPurseCommand implements Command
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
