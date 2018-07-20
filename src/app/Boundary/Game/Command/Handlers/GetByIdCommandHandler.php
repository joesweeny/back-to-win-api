<?php

namespace BackToWin\Boundary\Game\Command\Handlers;

use BackToWin\Boundary\Game\Command\GetByIdCommand;
use BackToWin\Boundary\Game\GamePresenter;
use BackToWin\Domain\Game\Orchestrator;
use BackToWin\Framework\Exception\NotFoundException;

class GetByIdCommandHandler
{
    /**
     * @var Orchestrator
     */
    private $orchestrator;
    /**
     * @var GamePresenter
     */
    private $presenter;

    public function __construct(Orchestrator $orchestrator, GamePresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetByIdCommand $command
     * @throws NotFoundException
     * @return \stdClass
     */
    public function handle(GetByIdCommand $command): \stdClass
    {
        $game = $this->orchestrator->getGameById($command->getGameId());

        return $this->presenter->toDto($game);
    }
}
