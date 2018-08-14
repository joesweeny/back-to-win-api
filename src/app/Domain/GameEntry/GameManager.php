<?php

namespace BackToWin\Domain\GameEntry;

use BackToWin\Bootstrap\Config;
use BackToWin\Domain\Bank\BankManager;
use BackToWin\Domain\Bank\Exception\BankingException;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\Exception\GameSettleException;
use BackToWin\Domain\GameEntry\Persistence\Repository;
use BackToWin\Domain\GameEntry\Services\EntryFeeStore;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Uuid\Uuid;
use Interop\Container\ContainerInterface;
use Money\Money;

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
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(
        Repository $repository,
        BankManager $bankManager,
        EntryFeeStore $feeStore,
        ContainerInterface $container
    ) {
        $this->repository = $repository;
        $this->bankManager = $bankManager;
        $this->feeStore = $feeStore;
        $this->container = $container;
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

    /**
     * Settle a Game by awarding the winning User the winning funds and depositing the remainder with the
     * Admin User account
     *
     * @param Game $game
     * @param User $user
     * @param Money $winnings
     * @throws GameSettleException
     * @return void
     */
    public function settleGame(Game $game, User $user, Money $winnings)
    {
        if (!$this->repository->exists($game->getId(), $user->getId())) {
            throw new GameSettleException(
                "Unable to settle Game {$game->getId()} as User {$user->getId()} has not entered Game"
            );
        }

        $this->handleWinnings($user, $winnings, $this->feeStore->getFeeTotal($game->getId()));
    }

    /**
     * @param User $user
     * @param Money $winnings
     * @param Money $total
     * @throws GameSettleException
     * @return void
     */
    private function handleWinnings(User $user, Money $winnings, Money $total)
    {
        try {
            $remainder = $total->subtract($winnings);
        } catch (\InvalidArgumentException $e) {
            throw new GameSettleException('Entry fee currency and winning currency mismatch');
        }

        $this->bankManager->deposit($user->getId(), $winnings);

        $this->bankManager->deposit(
            new Uuid($this->container->get(Config::class)->get('admin.user-id')),
            $remainder
        );
    }
}
