<?php

namespace BackToWin\Domain\UserPurse\Persistence\Hydration;

use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Framework\Calculation\Calculation;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;

class Hydrator
{
    /**
     * Hydrate a UserPurse object from a scalar object
     *
     * @param \stdClass $data
     * @return UserPurse
     */
    public static function hydratePurse(\stdClass $data): UserPurse
    {
        $purse = new UserPurse(
            Uuid::createFromBinary($data->user_id),
            new Money($data->amount, new Currency($data->currency))
        );

        $purse->setCreatedDate((new \DateTimeImmutable())->setTimestamp($data->created_at))
            ->setLastModifiedDate((new \DateTimeImmutable())->setTimestamp($data->updated_at));

        return $purse;
    }

    /**
     * Hydrate a UserPurseTransaction from a scalar object
     *
     * @param \stdClass $data
     * @return UserPurseTransaction
     */
    public static function hydrateTransaction(\stdClass $data): UserPurseTransaction
    {
        $transaction = (new UserPurseTransaction(Uuid::createFromBinary($data->id)))
            ->setUserId(Uuid::createFromBinary($data->user_id))
            ->setTotal(new Money($data->amount, new Currency($data->currency)))
            ->setCalculation(new Calculation($data->calculation))
            ->setCreatedDate((new \DateTimeImmutable())->setTimestamp($data->timestamp));

        if ($data->description !== null) {
            $transaction->setDescription($data->description);
        }

        return $transaction;
    }
}
