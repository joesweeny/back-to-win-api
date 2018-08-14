<?php

namespace BackToWin\Domain\GameEntry;

use BackToWin\Domain\Bank\BankManager;
use BackToWin\Domain\Bank\Exception\BankingException;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\Persistence\Repository;
use BackToWin\Domain\GameEntry\Services\EntryFeeStore;
use BackToWin\Domain\User\Entity\User;

class GameManager
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var EntryFeeStore
     */
    private $feeStore;
    /**
     * @var BankManager
     */
    private $bankManager;

    public function __construct(Repository $repository, BankManager $bankManager, EntryFeeStore $feeStore)
    {
        $this->repository = $repository;
        $this->bankManager = $bankManager;
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
        if (count($this->repository->get($game->getId())) >= $game->getPlayers()) {
            throw new GameEntryException("Game {$game->getId()} has reached full capacity");
        }

        try {
            $entryFee = $this->bankManager->withdraw($user, $game->getBuyIn());
        } catch (BankingException $e) {
            throw new GameEntryException(
                "User {$user->getId()} cannot enter Game {$game->getId()}. Message: {$e->getMessage()}"
            );
        }

        $entry = $this->repository->insert($game->getId(), $user->getId());

        $this->feeStore->enter($entry, $entryFee);
    }
}
