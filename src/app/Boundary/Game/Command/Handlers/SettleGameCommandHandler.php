<?php

namespace BackToWin\Boundary\Game\Command\Handlers;

use BackToWin\Boundary\Game\Command\SettleGameCommand;
use BackToWin\Domain\Game\Exception\GameSettlementException;
use BackToWin\Domain\Game\Services\GameKeeper;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Framework\Exception\NotFoundException;

class SettleGameCommandHandler
{
    /**
     * @var GameKeeper
     */
    private $keeper;
    /**
     * @var UserOrchestrator
     */
    private $userOrchestrator;

    public function __construct(GameKeeper $keeper, UserOrchestrator $userOrchestrator)
    {
        $this->keeper = $keeper;
        $this->userOrchestrator = $userOrchestrator;
    }

    /**
     * @param SettleGameCommand $command
     * @throws GameSettlementException
     * @throws NotFoundException
     * @return void
     */
    public function handle(SettleGameCommand $command)
    {
        $user = $this->userOrchestrator->getUserById($command->getUserId());

        $this->keeper->processGameSettlement($command->getGameId(), $user, $command->getMoney());
    }
}
