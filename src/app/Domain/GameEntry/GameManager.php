<?php

namespace BackToWin\Domain\GameEntry;

use BackToWin\Domain\Bank\Bank;
use BackToWin\Domain\Bank\Exception\BankingException;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\Persistence\Repository;
use BackToWin\Domain\GameEntry\Services\EntryFeeStore;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Uuid\Uuid;
use Money\Money;

class GameManager
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var Bank
     */
    private $bank;
    /**
     * @var EntryFeeStore
     */
    private $feeStore;

    public function __construct(Repository $repository, Bank $bank, EntryFeeStore $feeStore)
    {
        $this->repository = $repository;
        $this->bank = $bank;
        $this->feeStore = $feeStore;
    }

    /**
     * @param Game $game
     * @param User $user
     * @throws GameEntryException
     * @return void
     */
    public function addUserToGame(Game $game, User $user): void
    {
        if (count($this->repository->get($game->getId())) === $game->getPlayers()) {
            throw new GameEntryException("Game {$game->getId()} has reached full capacity");
        }

        $this->checkBalance($game->getId(), $user, $entryFee = $game->getBuyIn());

        $entry = $this->repository->insert($game->getId(), $user->getId());

        $this->feeStore->enter($entry, $this->bank->withdraw($user->getId(), $entryFee));
    }

    /**
     * @param Uuid $gameId
     * @param User $user
     * @param Money $entryFee
     * @throws GameEntryException
     */
    private function checkBalance(Uuid $gameId, User $user, Money $entryFee): void
    {
        try {
            $balance = $this->bank->getBalance($user->getId());
        } catch (BankingException $e) {
            throw new GameEntryException(
                "Game entry has failed for User {$user->getId()} with exception: {$e->getMessage()}",
                0,
                $e
            );
        }

        if (!$balance->greaterThan($entryFee)) {
            throw new GameEntryException("User {$user->getId()} does not have enough funds to enter Game {$gameId}");
        }
    }
}
