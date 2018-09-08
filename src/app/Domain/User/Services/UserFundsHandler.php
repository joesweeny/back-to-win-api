<?php

namespace GamePlatform\Domain\User\Services;

use GamePlatform\Domain\Bank\BankManager;
use GamePlatform\Domain\Bank\Exception\BankingException;
use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Exception\GameSettlementException;
use GamePlatform\Domain\GameEntry\Entity\GameEntry;
use GamePlatform\Domain\GameEntry\Exception\EntryFeeStoreException;
use GamePlatform\Domain\GameEntry\Exception\GameEntryException;
use GamePlatform\Domain\GameEntry\Services\EntryFee\EntryFeeStore;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\UserPurse\Entity\UserPurseTransaction;
use GamePlatform\Domain\UserPurse\UserPurseOrchestrator;
use GamePlatform\Framework\Calculation\Calculation;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Money;

class UserFundsHandler
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
            $balance = $this->bankManager->getBalance($user->getId());

            if (!$balance->isSameCurrency($game->getBuyIn())) {
                throw new GameEntryException(
                    'User cannot enter game due to Game currency and user bank currency mismatch'
                );
            }

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

        $this->updateUserPurse($user->getId(), $entryFee, Calculation::SUBTRACT(), "Game {$game->getId()} entry");
    }

    /**
     * Settle game winning funds by paying User and recording relevant transactions
     *
     * @param Uuid $gameId
     * @param Uuid $userId
     * @param Money $winnings
     * @return Money
     *  Total entry pot minus winning total
     * @throws GameSettlementException
     */
    public function settleGameWinnings(Uuid $gameId, Uuid $userId, Money $winnings): Money
    {
        try {
            $pot = $this->feeStore->getFeeTotal($gameId);
        } catch (EntryFeeStoreException $e) {
            throw new GameSettlementException("Unable to settle Game {$gameId}. Message {$e->getMessage()}");
        }

        $this->bankManager->deposit($userId, $winnings);

        $this->updateUserPurse($userId, $winnings, Calculation::ADD(), "Game {$gameId} winning total");

        $this->feeStore->delete($gameId);

        return $pot->subtract($winnings);
    }

    /**
     * @param Uuid $userId
     * @param Money $money
     * @param Calculation $calculation
     * @param string $message
     * @throws NotFoundException
     * @return void
     */
    private function updateUserPurse(Uuid $userId, Money $money, Calculation $calculation, string $message): void
    {
        $this->purseOrchestrator->createTransaction(
            (new UserPurseTransaction())
                ->setUserId($userId)
                ->setTotal($money)
                ->setCalculation($calculation)
                ->setDescription($message)
        );

        $purse = $this->purseOrchestrator->getUserPurse($userId);

        $purse = $calculation->equals(Calculation::ADD()) ? $purse->addMoney($money) : $purse->subtractMoney($money);

        $this->purseOrchestrator->updateUserPurse($purse);
    }
}
