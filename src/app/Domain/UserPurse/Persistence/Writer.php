<?php

namespace BackToWin\Domain\UserPurse\Persistence;

use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Framework\Exception\NotFoundException;

interface Writer
{
    /**
     * Insert a new UserPurse record into the database
     *
     * @param UserPurse $purse
     * @return void
     */
    public function insert(UserPurse $purse): void;

    /**
     * Update an existing UserPurse record in the database
     *
     * @param UserPurse $purse
     * @throws NotFoundException
     * @return void
     */
    public function update(UserPurse $purse): void;

    /**
     * Insert a UserPurseTransaction record in the database
     *
     * @param UserPurseTransaction $transaction
     * @return void
     */
    public function insertTransaction(UserPurseTransaction $transaction): void;
}
