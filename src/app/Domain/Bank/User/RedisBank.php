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

        $this->insert($userId, $money);
    }

    /**
     * @inheritdoc
     */
    public function deposit(Uuid $userId, Money $money): void
    {
        $balance = $this->getBalance($userId);

        if (!$balance->isSameCurrency($money)) {
            throw new BankingException(
                "Cannot deposit money as account currency does not match deposit currency for User {$userId}"
            );
        }

        $this->insert($userId, $newBalance = $balance->add($money));
    }

    /**
     * @inheritdoc
     */
    public function withdraw(Uuid $userId, Money $money): Money
    {
        $balance = $this->getBalance($userId);

        if (!$balance->isSameCurrency($money)) {
            throw new BankingException(
                "Cannot withdraw money as account currency does not match deposit currency for User {$userId}"
            );
        }

        if ($money->greaterThan($balance)) {
            throw new BankingException("Cannot withdraw money due to insufficient funds: User {$userId}");
        }

        $this->insert($userId, $newBalance = $balance->subtract($money));
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

    private function insert(Uuid $userId, Money $money)
    {
        $this->client->set((string) $userId, json_encode($money->jsonSerialize()));
    }
}
