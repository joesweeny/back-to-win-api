<?php

namespace BackToWin\Domain\GameEntry\Services;

use BackToWin\Domain\Game\Entity\Game;
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
    public function create(Game $game): void
    {
        if ($this->exists($game->getId())) {
            throw new EntryFeeStoreException("Entry fee store record already exists for {$game->getId()}");
        }

        $this->client->set((string) $game->getId(), null);
    }

    /**
     * @inheritdoc
     */
    public function enter(GameEntry $entry, Money $fee): void
    {
        if (!$this->client->exists($key = (string) $entry->getGameId())) {
            throw new EntryFeeStoreException(
                "Cannot enter fee for User {$entry->getUserId()} as Game {$entry->getGameId()} record does not exist"
            );
        }

        $game = $this->get($entry->getGameId());

        $record = (string) $entry->getGameId() . json_encode($fee->jsonSerialize());

        $this->client->set($key, $game ? $game . ':' . $record : $record);
    }

    /**
     * @inheritdoc
     */
    public function getFeeTotal(Uuid $gameId): Money
    {
        if (!$this->client->exists($key = (string) $gameId)) {
            throw new EntryFeeStoreException("Game {$gameId} record does not exist");
        }

        $total = 0;

        array_map(function (array $money) use (&$total) {
            $total += $money['money'];
        }, $value = explode(':', $this->get($gameId)));

        return new Money($total, new Currency($value[0]['currency']));
    }

    /**
     * @inheritdoc
     */
    public function delete(Uuid $gameId): void
    {
        $this->client->del([(string) $gameId]);
    }

    private function exists(Uuid $gameId): string
    {
        return $this->client->exists((string) $gameId);
    }

    private function get(Uuid $gameId): ?string
    {
        return $this->client->get((string) $gameId);
    }
}
