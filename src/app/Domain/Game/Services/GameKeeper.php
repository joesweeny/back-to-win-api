<?php

namespace BackToWin\Domain\Game\Services;

use BackToWin\Domain\Admin\Bank\Services\FundsHandler;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Exception\GameSettlementException;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\GameEntryOrchestrator;
use BackToWin\Domain\User\Services\UserFundsHandler;
use BackToWin\Domain\User\Entity\User;
use Money\Money;

class GameKeeper
{
    /**
     * @var GameEntryOrchestrator
     */
    private $entryOrchestrator;
    /**
     * @var UserFundsHandler
     */
    private $fundsHandler;
    /**
     * @var FundsHandler
     */
    private $handler;

    public function __construct(
        GameEntryOrchestrator $entryOrchestrator,
        UserFundsHandler $fundsHandler,
        FundsHandler $handler
    ) {
        $this->entryOrchestrator = $entryOrchestrator;
        $this->fundsHandler = $fundsHandler;
        $this->handler = $handler;
    }

    /**
     * Check that a User is eligible to join a Game, if so use handler class to process funds withdrawal
     *
     * @param Game $game
     * @param User $user
     * @throws GameEntryException
     * @throws \RuntimeException
     * @return void
     */
    public function processUserGameEntry(Game $game, User $user): void
    {
        $this->entryOrchestrator->checkEntryEligibility($game, $user->getId());

        $this->fundsHandler->handleGameEntryFee($game, $user);

        $this->entryOrchestrator->addGameEntry($game, $user);
    }

    /**
     * Settle Game by releasing and distributing funds and recording relevant transactions
     *
     * @param Game $game
     * @param User $user
     * @param Money $winningTotal
     * @throws GameSettlementException
     * @return void
     */
    public function processGameSettlement(Game $game, User $user, Money $winningTotal): void
    {
        if ($this->entryOrchestrator->isUserInGame($game, $user->getId())) {
            throw new GameSettlementException(
                "Unable to settle as User {$user->getId()} did not enter Game {$game->getId()}"
            );
        }

        $money = $this->fundsHandler->settleGameWinnings($game->getId(), $user->getId(), $winningTotal);

        $this->handler->addSettledGameFunds($game->getId(), $money);
    }
}
