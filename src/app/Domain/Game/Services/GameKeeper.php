<?php

namespace BackToWin\Domain\Game\Services;

use BackToWin\Domain\Admin\Bank\Services\FundsHandler;
use BackToWin\Domain\Game\Exception\GameSettlementException;
use BackToWin\Domain\Game\GameOrchestrator;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\GameEntryOrchestrator;
use BackToWin\Domain\User\Services\UserFundsHandler;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;
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
    /**
     * @var GameOrchestrator
     */
    private $gameOrchestrator;

    public function __construct(
        GameOrchestrator $gameOrchestrator,
        GameEntryOrchestrator $entryOrchestrator,
        UserFundsHandler $fundsHandler,
        FundsHandler $handler
    ) {
        $this->gameOrchestrator = $gameOrchestrator;
        $this->entryOrchestrator = $entryOrchestrator;
        $this->fundsHandler = $fundsHandler;
        $this->handler = $handler;
    }

    /**
     * Use the relevant Orchestrator and subclasses to process the entry of a User into a Game
     *
     * @param Uuid $gameId
     * @param User $user
     * @throws GameEntryException
     * @throws NotFoundException
     * @return void
     */
    public function processUserGameEntry(Uuid $gameId, User $user): void
    {
        $game = $this->gameOrchestrator->getGameToEnter($gameId);

        $this->entryOrchestrator->checkEntryEligibility($game, $user->getId());

        $this->fundsHandler->handleGameEntryFee($game, $user);

        $this->entryOrchestrator->addGameEntry($game, $user);
    }

    /**
     * Use the relevant Orchestrator and subclasses to process the settlement of a Game
     *
     * @param Uuid $gameId
     * @param User $user
     * @param Money $winningTotal
     * @throws GameSettlementException
     * @return void
     */
    public function processGameSettlement(Uuid $gameId, User $user, Money $winningTotal): void
    {
        $game = $this->gameOrchestrator->getGameToSettle($gameId);

        if (!$this->entryOrchestrator->isUserInGame($game, $user->getId())) {
            throw new GameSettlementException(
                "Unable to settle as User {$user->getId()} did not enter Game {$game->getId()}"
            );
        }

        $money = $this->fundsHandler->settleGameWinnings($game->getId(), $user->getId(), $winningTotal);

        $this->handler->addSettledGameFunds($game->getId(), $money);
    }
}
