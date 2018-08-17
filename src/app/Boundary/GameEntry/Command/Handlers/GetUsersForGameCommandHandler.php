<?php

namespace BackToWin\Boundary\GameEntry\Command\Handlers;

use BackToWin\Boundary\Game\GamePresenter;
use BackToWin\Boundary\GameEntry\Command\GetUsersForGameCommand;
use BackToWin\Boundary\User\UserPresenter;
use BackToWin\Domain\Game\GameOrchestrator;
use BackToWin\Domain\GameEntry\GameEntryOrchestrator;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Exception\NotFoundException;

class GetUsersForGameCommandHandler
{
    /**
     * @var GameEntryOrchestrator
     */
    private $orchestrator;
    /**
     * @var GameOrchestrator
     */
    private $gameOrchestrator;
    /**
     * @var GamePresenter
     */
    private $gamePresenter;
    /**
     * @var UserPresenter
     */
    private $userPresenter;

    public function __construct(
        GameEntryOrchestrator $orchestrator,
        GameOrchestrator $gameOrchestrator,
        GamePresenter $gamePresenter,
        UserPresenter $userPresenter
    ) {
        $this->orchestrator = $orchestrator;
        $this->gameOrchestrator = $gameOrchestrator;
        $this->gamePresenter = $gamePresenter;
        $this->userPresenter = $userPresenter;
    }

    /**
     * @param GetUsersForGameCommand $command
     * @throws NotFoundException
     * @return array|\stdClass[]
     */
    public function handle(GetUsersForGameCommand $command): array
    {
        $game = $this->gameOrchestrator->getGameById($command->getGameId());

        $users = array_map(function (User $user) {
            return $this->userPresenter->toDto($user);
        }, $this->orchestrator->getUsersForGame($game->getId()));

        return [
            'game' => $this->gamePresenter->toDto($game),
            'users' => $users
        ];
    }
}
