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

    public function setUserId(Uuid $userId): self
    {
        return $this->set('user_id', $userId);
    }

    public function getUserId(): Uuid
    {
        return $this->getOrFail('user_id');
    }

    public function setTotal(Money $money): self
    {
        return $this->set('total', $money);
    }

    public function getTotal(): Money
    {
        return $this->getOrFail('total');
    }
}
