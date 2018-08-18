<?php

namespace BackToWin\Boundary\Game\Command\Handlers;

use BackToWin\Boundary\Game\Command\EnterGameCommand;
use BackToWin\Domain\Game\Services\GameKeeper;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Framework\Exception\NotFoundException;

class EnterGameCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $userOrchestrator;
    /**
     * @var GameKeeper
     */
    private $keeper;

    public function __construct(GameKeeper $keeper, UserOrchestrator $userOrchestrator)
    {
        $this->userOrchestrator = $userOrchestrator;
        $this->keeper = $keeper;
    }

    /**
     * @param EnterGameCommand $command
     * @throws NotFoundException
     * @throws GameEntryException
     * @return void
     */
    public function handle(EnterGameCommand $command): void
    {
        $user = $this->userOrchestrator->getUserById($command->getUserId());

        $this->keeper->processUserGameEntry($command->getGameId(), $user);
    }
}
