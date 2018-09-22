<?php

namespace BackToWin\Boundary\Avatar\Command;

use Chief\Command;
use BackToWin\Framework\Uuid\Uuid;

class AddAvatarCommand implements Command
{
    /**
     * @var Uuid
     */
    private $userId;
    /**
     * @var string
     */
    private $filename;
    /**
     * @var string
     */
    private $contents;

    public function __construct(string $userId, string $filename, string $contents)
    {
        $this->userId = new Uuid($userId);
        $this->filename = $filename;
        $this->contents = $contents;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getContents(): string
    {
        return $this->contents;
    }
}
