<?php

namespace GamePlatform\Domain\Admin\Bank\Redis;

use GamePlatform\Domain\Admin\Bank\Bank;
use GamePlatform\Domain\Admin\Bank\Exception\BankingException;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use Predis\Client;

class RedisBank implements Bank
{
    const KEY = 'admin-bank';

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
        if ($this->client->exists(self::KEY . ':' . (string) $gameId)) {
            throw new BankingException("Record for Game {$gameId} already exists");
        }

        $this->client->set(self::KEY . ':' . (string) $gameId, json_encode($money->jsonSerialize()));
    }

    public function getBalance(): Money
    {
        /** @var Money[] $objects */
        $objects = array_map(function (string $key) {
            $value = (object) json_decode($this->client->get($key));

            return new Money($value->amount, new Currency($value->currency));
        }, $this->client->keys('*'. self::KEY . '*'));

        $money = new Money(0, $objects[0]->getCurrency());

        foreach ($objects as $object) {
            $money = $money->add($object);
        }

        return $money;
    }
}
