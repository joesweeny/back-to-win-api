<?php

namespace GamePlatform\Domain\Game\Services;

use GamePlatform\Domain\Admin\Bank\Services\FundsHandler;
use GamePlatform\Domain\Game\Exception\GameSettlementException;
use GamePlatform\Domain\Game\GameOrchestrator;
use GamePlatform\Domain\GameEntry\Exception\GameEntryException;
use GamePlatform\Domain\GameEntry\GameEntryOrchestrator;
use GamePlatform\Domain\User\Services\UserFundsHandler;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Framework\DateTime\Clock;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Uuid\Uuid;
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
    /**
     * @var Clock
     */
    private $clock;

    public function __construct(
        GameOrchestrator $gameOrchestrator,
        GameEntryOrchestrator $entryOrchestrator,
        UserFundsHandler $fundsHandler,
        FundsHandler $handler,
        Clock $clock
    ) {
        $this->gameOrchestrator = $gameOrchestrator;
        $this->entryOrchestrator = $entryOrchestrator;
        $this->fundsHandler = $fundsHandler;
        $this->handler = $handler;
        $this->clock = $clock;
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
     * @throws NotFoundException
     * @return void
     */
    public function processGameSettlement(Uuid $gameId, User $user, Money $winningTotal): void
    {
        $game = $this->gameOrchestrator->getGameToSettle($gameId);

        if ($game->getStartDateTime() >= $this->clock->now()) {
            throw new GameSettlementException("Cannot settle Game {$gameId} as the game has not started yet");
        }

        if (!$this->entryOrchestrator->isUserInGame($game, $user->getId())) {
            throw new GameSettlementException(
                "Unable to settle as User {$user->getId()} did not enter Game {$game->getId()}"
            );
        }

        $money = $this->fundsHandler->settleGameWinnings($game->getId(), $user->getId(), $winningTotal);

        $this->handler->addSettledGameFunds($game->getId(), $money);

        $this->gameOrchestrator->completeGame($game, $user->getId());
    }
}
