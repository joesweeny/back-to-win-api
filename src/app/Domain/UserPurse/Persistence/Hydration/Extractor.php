<?php

namespace BackToWin\Domain\UserPurse\Persistence\Hydration;

use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Framework\Exception\UndefinedValueException;

class Extractor
{
    /**
     * Convert a UserPurse object into a scalar object
     *
     * @param UserPurse $purse
     * @throws UndefinedValueException
     * @return \stdClass
     */
    public static function purseToRawData(UserPurse $purse): \stdClass
    {
        return (object) [
            'user_id' => $purse->getUserId()->toBinary(),
            'currency' => $purse->getTotal()->getCurrency()->getCode(),
            'amount' => (int) $purse->getTotal()->getAmount(),
            'created_at' => $purse->getCreatedDate()->getTimestamp(),
            'updated_at' => $purse->getLastModifiedDate()->getTimestamp(),
        ];
    }

    /**
     * Convert a UserPurseTransaction object into a scalar object
     *
     * @param UserPurseTransaction $transaction
     * @throws UndefinedValueException
     * @return \stdClass
     */
    public static function transactionToRawData(UserPurseTransaction $transaction): \stdClass
    {
        return (object) [
            'id' => $transaction->getId()->toBinary(),
            'user_id' => $transaction->getUserId()->toBinary(),
            'currency' => $transaction->getTotal()->getCurrency()->getCode(),
            'amount' => (int) $transaction->getTotal()->getAmount(),
            'calculation' => $transaction->getCalculation()->getValue(),
            'description' => $transaction->getDescription(),
            'timestamp' => $transaction->getCreatedDate()->getTimestamp()
        ];
    }
}
