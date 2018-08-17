<?php

namespace BackToWin\Domain\GameEntry\Services;

use BackToWin\Domain\Bank\BankManager;
use BackToWin\Domain\Bank\Exception\BankingException;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Exception\EntryFeeStoreException;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\Services\EntryFee\EntryFeeStore;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Domain\UserPurse\UserPurseOrchestrator;
use BackToWin\Framework\Calculation\Calculation;

class GameEntryManager
{
    /**
     * @var EntryFeeStore
     */
    private $feeStore;
    /**
     * @var BankManager
     */
    private $bankManager;

    /**
     * @var UserPurseOrchestrator
     */
    private $purseOrchestrator;

    public function __construct(
        BankManager $bankManager,
        EntryFeeStore $feeStore,
        UserPurseOrchestrator $purseOrchestrator
    ) {
        $this->bankManager = $bankManager;
        $this->feeStore = $feeStore;
        $this->purseOrchestrator = $purseOrchestrator;
    }

    /**
     * Take funds from User bank and place into EntryFeeStore and add user purse transaction record
     *
     * @param Game $game
     * @param User $user
     * @throws GameEntryException
     * @throws \RuntimeException
     * @return void
     */
    public function handleGameEntryFee(Game $game, User $user): void
    {
        try {
            $entryFee = $this->bankManager->withdraw($user, $game->getBuyIn());
        } catch (BankingException $e) {
            throw new GameEntryException(
                "User {$user->getId()} cannot enter Game {$game->getId()}. Message: {$e->getMessage()}"
            );
        }

        try {
            $this->feeStore->enter(new GameEntry($game->getId(), $user->getId()), $entryFee);
        } catch (EntryFeeStoreException $e) {
            throw new \RuntimeException('There has been an internal error');
        }

        $this->purseOrchestrator->createTransaction(
            (new UserPurseTransaction())
                ->setUserId($user->getId())
                ->setTotal($entryFee)
                ->setCalculation(Calculation::SUBTRACT())
                ->setDescription("Game {$game->getId()} entry")
        );
    }
}
