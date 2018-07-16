<?php

namespace BackToWin\Boundary\UserPurse;

use BackToWin\Domain\UserPurse\Entity\UserPurse;

class UserPursePresenter
{
    public function toDto(UserPurse $purse): \stdClass
    {
        return (object) [
            'user_id' => (string) $purse->getUserId(),
            'currency' => $purse->getTotal()->getCurrency()->getCode(),
            'amount' => (int) $purse->getTotal()->getAmount()
        ];
    }
}
