<?php

namespace BackToWin\Domain\Admin\Bank\Redis;

use BackToWin\Domain\Admin\Bank\Bank;
use BackToWin\Domain\Admin\Bank\Exception\BankingException;
use BackToWin\Framework\Uuid\Uuid;
use Money\Money;
use Predis\Client;

class RedisBank implements Bank
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->client->connect();
    }

    /**
     * @inheritdoc
     */
    public function deposit(Uuid $gameId, Money $money): void
    {
        if ($this->client->exists((string) $gameId)) {
            throw new BankingException("Record for Game {$gameId} already exists");
        }

        $this->client->set((string) $gameId, json_encode($money->jsonSerialize()));
    }
}
