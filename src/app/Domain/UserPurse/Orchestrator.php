<?php

namespace BackToWin\Domain\UserPurse;

use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Persistence\Reader;
use BackToWin\Domain\UserPurse\Persistence\Writer;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;

class Orchestrator
{
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var Writer
     */
    private $writer;

    public function __construct(Reader $reader, Writer $writer)
    {
        $this->reader = $reader;
        $this->writer = $writer;
    }

    public function createUserPurse(UserPurse $purse): void
    {
        $this->writer->insert($purse);
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
