<?php

namespace BackToWin\Boundary\GameEntry\Command\Handlers;

use BackToWin\Boundary\GameEntry\Command\EnterGameCommand;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\Orchestrator;
use BackToWin\Framework\Exception\NotFoundException;

class EnterGameCommandHandler
{
    /**
     * @var Orchestrator
     */
    private $orchestrator;

    public function __construct(Orchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param EnterGameCommand $command
     * @throws NotFoundException
     * @throws GameEntryException
     * @return void
     */
    public function handle(EnterGameCommand $command)
    {
        $this->orchestrator->enterGame($command->getGameId(), $command->getUserId());
    }
}
