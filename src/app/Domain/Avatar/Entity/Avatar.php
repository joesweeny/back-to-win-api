<?php

namespace BackToWin\Domain\Avatar\Entity;

use BackToWin\Framework\Entity\PrivateAttributesTrait;
use BackToWin\Framework\Entity\TimestampedTrait;
use BackToWin\Framework\Uuid\Uuid;

class Avatar
{
    use PrivateAttributesTrait,
        TimestampedTrait;

    /**
     * @var Uuid
     */
    private $userId;
    /**
     * @var string
     */
    private $filename;

    public function __construct(Uuid $userId, string $filename)
    {
        $this->userId = $userId;
        $this->filename = $filename;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFileContents(string $contents): self
    {
        return $this->set('contents', $contents);
    }

    public function getFileContents(): ?string
    {
        return $this->get('contents');
    }
}
