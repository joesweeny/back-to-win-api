<?php

namespace BackToWin\Domain\UserPurse;

use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Persistence\Reader;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;

class Orchestrator
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param Uuid $userId
     * @throws NotFoundException
     * @return UserPurse
     */
    public function getUserPurse(Uuid $userId): UserPurse
    {
        return $this->reader->getPurse($userId);
    }
}
