<?php

namespace BackToWin\Domain\GameEntry\Services;

use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Exception\EntryFeeStoreException;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use Predis\Client;

class RedisEntryFeeStore implements EntryFeeStore
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
    public function enter(GameEntry $entry, Money $fee): void
    {
        $record = (string) $entry->getUserId() . '.' . json_encode($fee->jsonSerialize());

        $this->exists($entry->getGameId())
            ? $this->update($entry->getGameId(), $record)
            : $this->create($entry->getGameId(), $record);
    }

    /**
     * @inheritdoc
     */
    public function getFeeTotal(Uuid $gameId): Money
    {
        if (!$gameRecord = $this->get($gameId)) {
            throw new EntryFeeStoreException("Game {$gameId} record does not exist");
        }

        $money = array_map(function (string $record) {
            $money = json_decode(explode('.', $record)[1]);

            return new Money($money->amount, new Currency($money->currency));
        }, explode('/', $gameRecord));

        $total = new Money(0, $money[0]->getCurrency());

        foreach ($money as $object) {
            $total = $total->add($object);
        }

        return $total;
    }

    public function delete(Uuid $gameId): void
    {
        $this->client->del([(string) $gameId]);
    }

    private function exists(Uuid $gameId): bool
    {
        return (bool) $this->client->exists((string) $gameId);
    }

    private function get(Uuid $gameId): ?string
    {
        return $this->client->get((string) $gameId);
    }

    private function create(Uuid $gameId, string $record)
    {
        $this->client->set((string) $gameId, $record);
    }

    private function update(Uuid $gameId, string $record)
    {
        if (!$existing = $this->get($gameId)) {
            throw new EntryFeeStoreException("Cannot update record for Game {$gameId} - record does not exist");
        }

        $this->client->set((string) $gameId, $existing . '/' . $record);
    }
}
