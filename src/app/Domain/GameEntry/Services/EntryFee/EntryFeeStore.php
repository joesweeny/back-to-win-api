<?php

namespace GamePlatform\Domain\GameEntry\Services\EntryFee;

use GamePlatform\Domain\GameEntry\Entity\GameEntry;
use GamePlatform\Domain\GameEntry\Exception\EntryFeeStoreException;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Money;

interface EntryFeeStore
{
    /**
     * Sort entry fee against GameEntry i.e. Game ID and User ID
     *
     * @param GameEntry $entry
     * @param Money $fee
     * @throws EntryFeeStoreException
     */
    public function enter(GameEntry $entry, Money $fee): void;

    /**
     * Return the total value for Game
     *
     * @param Uuid $gameId
     * @throws EntryFeeStoreException
     * @return Money
     */
    public function getFeeTotal(Uuid $gameId): Money;

    /**
     * Delete a record from the store
     *
     * @param Uuid $gameId
     * @return void
     */
    public function delete(Uuid $gameId): void;
}
