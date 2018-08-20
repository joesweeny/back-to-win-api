<?php

namespace GamePlatform\Domain\UserPurse\Entity;

use GamePlatform\Framework\Entity\PrivateAttributesTrait;
use GamePlatform\Framework\Entity\TimestampedTrait;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Money;

class UserPurse
{
    use PrivateAttributesTrait,
        TimestampedTrait;

    /**
     * @var Uuid
     */
    private $userId;
    /**
     * @var Money
     */
    private $money;

    public function __construct(Uuid $userId, Money $money)
    {
        $this->userId = $userId;
        $this->money = $money;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getTotal(): Money
    {
        return $this->money;
    }

    /**
     * @param Money $money
     * @return UserPurse
     * @throws \InvalidArgumentException
     * @return UserPurse
     */
    public function addMoney(Money $money): UserPurse
    {
        $this->money = $this->money->add($money);

        return $this;
    }

    /**
     * @param Money $money
     * @return UserPurse
     * @throws \InvalidArgumentException
     * @return UserPurse
     */
    public function subtractMoney(Money $money): UserPurse
    {
        $this->money = $this->money->subtract($money);

        return $this;
    }
}
