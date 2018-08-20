<?php

namespace GamePlatform\Boundary\Game\Command\Handlers;

use GamePlatform\Boundary\Game\Command\SettleGameCommand;
use GamePlatform\Domain\Game\Exception\GameSettlementException;
use GamePlatform\Domain\Game\Services\GameKeeper;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Framework\Exception\NotFoundException;

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
