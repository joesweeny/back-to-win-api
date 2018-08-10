<?php

namespace BackToWin\Domain\GameEntry\Services;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Exception\EntryFeeStoreException;
use BackToWin\Framework\Uuid\Uuid;
use Money\Money;

interface EntryFeeStore
{
    /**
     * @param Game $game
     * @throws  EntryFeeStoreException
     * @return void
     */
    public function create(Game $game): void;

    /**
     * @param GameEntry $entry
     * @param Money $fee
     * @throws EntryFeeStoreException
     */
    public function enter(GameEntry $entry, Money $fee): void;

    /**
     * Return the winning value for Game
     *
     * @param Uuid $gameId
     * @return Money
     */
    public function getFeeTotal(Uuid $gameId): Money;

    /**
     * Delete Game record from store
     *
     * @param Uuid $gameId
     * @throws EntryFeeStoreException
     */
    public function delete(Uuid $gameId): void;
}
