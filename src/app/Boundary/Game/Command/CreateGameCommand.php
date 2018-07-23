<?php

namespace BackToWin\Boundary\Game\Command;

use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use Chief\Command;
use Money\Currency;
use Money\Money;

class CreateGameCommand implements Command
{
    /**
     * @var GameType
     */
    private $type;

    /**
     * @var Currency
     */
    private $currency;
    /**
     * @var int
     */
    private $max;
    /**
     * @var int
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
    /**
     * @var int
     */
    private $buyIn;

    public function __construct(
        string $type,
        string $currency,
        int $buyIn,
        int $max,
        int $min,
        string $start,
        int $players
    ) {
        $this->type = new GameType($type);
        $this->currency = new Currency($currency);
        $this->buyIn = $buyIn;
        $this->max = $max;
        $this->min = $min;
        $this->start = new \DateTimeImmutable($start);
        $this->players = $players;
    }

    public function getType(): GameType
    {
        return $this->type;
    }

    public function getBuyIn(): Money
    {
        return new Money($this->buyIn, $this->currency);
    }

    public function getMax(): Money
    {
        return new Money($this->max, $this->currency);
    }

    public function getMin(): Money
    {
        return new Money($this->min, $this->currency);
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
