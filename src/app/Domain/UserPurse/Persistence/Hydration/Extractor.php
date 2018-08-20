<?php

namespace GamePlatform\Domain\UserPurse\Persistence\Hydration;

use GamePlatform\Domain\UserPurse\Entity\UserPurse;
use GamePlatform\Domain\UserPurse\Entity\UserPurseTransaction;
use GamePlatform\Framework\Exception\UndefinedValueException;

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
