<?php

namespace BackToWin\Domain\UserPurse\Entity;

use BackToWin\Framework\Entity\PrivateAttributesTrait;
use BackToWin\Framework\Entity\TimestampedTrait;
use BackToWin\Framework\Uuid\Uuid;
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
}
