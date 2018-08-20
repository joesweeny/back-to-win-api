<?php

namespace GamePlatform\Boundary\Game\Command;

use GamePlatform\Framework\Uuid\Uuid;
use Chief\Command;
use Money\Currency;
use Money\Money;

class SettleGameCommand implements Command
{
    /**
     * @var Uuid
     */
    private $gameId;
    /**
     * @var Uuid
     */
    private $userId;
    /**
     * @var string
     */
    private $currency;
    /**
     * @var int
     */
    private $total;

    public function __construct(string $gameId, string $userId, string $currency, int $total)
    {
        $this->gameId = new Uuid($gameId);
        $this->userId = new Uuid($userId);
        $this->currency = $currency;
        $this->total = $total;
    }

    public function getGameId(): Uuid
    {
        return $this->gameId;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getMoney(): Money
    {
        return new Money($this->total, new Currency($this->currency));
    }
}
