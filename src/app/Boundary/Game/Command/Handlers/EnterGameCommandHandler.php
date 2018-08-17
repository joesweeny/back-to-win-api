<?php

namespace BackToWin\Boundary\Game\Command\Handlers;

use BackToWin\Boundary\Game\Command\EnterGameCommand;
use BackToWin\Domain\Game\GameOrchestrator;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Framework\Exception\NotFoundException;

class EnterGameCommandHandler
{
    /**
     * @var GameOrchestrator
     */
    private $gameOrchestrator;
    /**
     * @var UserOrchestrator
     */
    private $userOrchestrator;

    public function __construct(GameOrchestrator $gameOrchestrator, UserOrchestrator $userOrchestrator)
    {
        $this->gameOrchestrator = $gameOrchestrator;
        $this->userOrchestrator = $userOrchestrator;
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

        $this->gameOrchestrator->addUserToGame($command->getGameId(), $user);
    }
}
