<?php

namespace GamePlatform\Boundary\Game\Command\Handlers;

use GamePlatform\Boundary\Game\Command\EnterGameCommand;
use GamePlatform\Domain\Game\Services\GameKeeper;
use GamePlatform\Domain\GameEntry\Exception\GameEntryException;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Framework\Exception\NotFoundException;

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
