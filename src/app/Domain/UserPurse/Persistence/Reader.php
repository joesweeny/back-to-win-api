<?php

namespace GamePlatform\Domain\UserPurse\Persistence;

use GamePlatform\Domain\UserPurse\Entity\UserPurse;
use GamePlatform\Domain\UserPurse\Entity\UserPurseTransaction;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Uuid\Uuid;

interface Reader
{
    /**
     * @param Uuid $userId
     * @throws NotFoundException
     * @return UserPurse
     */
    public function getPurse(Uuid $userId): UserPurse;

    /**
     * @param Uuid $transactionId
     * @throws NotFoundException
     * @return UserPurseTransaction
     */
    public function getTransaction(Uuid $transactionId): UserPurseTransaction;

    /**
     * @param Uuid $userId
     * @return array|UserPurseTransaction[]
     */
    public function getTransactionsForUser(Uuid $userId): array;
}
