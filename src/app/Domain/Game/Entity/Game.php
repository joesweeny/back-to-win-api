<?php

namespace BackToWin\Domain\Game\Entity;

use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Framework\Entity\PrivateAttributesTrait;
use BackToWin\Framework\Entity\TimestampedTrait;
use BackToWin\Framework\Uuid\Uuid;
use Money\Money;

class Game
{
    use PrivateAttributesTrait,
        TimestampedTrait;

    /**
     * @var Uuid
     */
    private $id;
    /**
     * @var GameType
     */
    private $type;
    /**
     * @var GameStatus
     */
    private $status;
    /**
     * @var Money
     */
    private $max;
    /**
     * @var Money
     */
    private $min;
    /**
     * @var \DateTimeImmutable
     */
    private $start;
    /**
     * @var int
     */
    private $players;

    public function __construct(
        Uuid $id,
        GameType $type,
        GameStatus $status,
        Money $max,
        Money $min,
        \DateTimeImmutable $start,
        int $players
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->status = $status;
        $this->max = $max;
        $this->min = $min;
        $this->start = $start;
        $this->players = $players;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getType(): GameType
    {
        return $this->type;
    }

    public function getStatus(): GameStatus
    {
        return $this->status;
    }

    public function getMax(): Money
    {
        return $this->max;
    }
    
    public function getMin(): Money
    {
        return $this->min;
    }

    public function getStartDateTime(): \DateTimeImmutable
    {
        return $this->start;
    }

    public function getPlayers(): int
    {
        return $this->players;
    }
}
