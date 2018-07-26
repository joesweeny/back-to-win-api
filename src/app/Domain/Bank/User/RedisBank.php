<?php

namespace BackToWin\Domain\Bank\User;

use BackToWin\Domain\Bank\Bank;
use BackToWin\Domain\Bank\Exception\BankingException;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
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
    public function openAccount(Uuid $userId, Money $money): void
    {
        if ($this->client->exists((string) $userId)) {
            throw new BankingException("Cannot open bank account for User {$userId} - already exists");
        }

        $this->client->set((string) $userId, json_encode($money->jsonSerialize()));
    }

    /**
     * @inheritdoc
     */
    public function deposit(Uuid $userId, Money $money): void
    {
        // TODO: Implement deposit() method.
    }

    /**
     * @inheritdoc
     */
    public function withdraw(Uuid $userId, Money $money): void
    {
        // TODO: Implement withdraw() method.
    }

    /**
     * @inheritdoc
     */
    public function getBalance(Uuid $userId): Money
    {
        if (!$this->client->exists((string) $userId)) {
            throw new BankingException("Bank account for User {$userId} does not exist");
        }

        $value = (object) json_decode($this->client->get((string) $userId));

        if (!isset($value->amount, $value->currency)) {
            throw new BankingException("Bank account balance for User {$userId} is not in the correct format");
        }

        return new Money($value->amount, new Currency($value->currency));
    }
}