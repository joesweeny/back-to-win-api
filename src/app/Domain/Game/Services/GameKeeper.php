<?php

namespace BackToWin\Domain\Game\Services;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\GameEntryOrchestrator;
use BackToWin\Domain\User\Services\UserFundsHandler;
use BackToWin\Domain\User\Entity\User;

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

    public function __construct(GameEntryOrchestrator $entryOrchestrator, UserFundsHandler $fundsHandler)
    {
        $this->entryOrchestrator = $entryOrchestrator;
        $this->fundsHandler = $fundsHandler;
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
}
