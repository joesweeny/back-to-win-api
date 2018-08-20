<?php

namespace GamePlatform\Boundary\GameEntry\Command\Handlers;

use GamePlatform\Boundary\Game\GamePresenter;
use GamePlatform\Boundary\GameEntry\Command\GetUsersForGameCommand;
use GamePlatform\Boundary\User\UserPresenter;
use GamePlatform\Domain\Game\GameOrchestrator;
use GamePlatform\Domain\GameEntry\GameEntryOrchestrator;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Framework\Exception\NotFoundException;

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
     * @return array
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
