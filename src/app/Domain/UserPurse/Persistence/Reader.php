<?php

namespace BackToWin\Domain\UserPurse\Persistence;

use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;

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
