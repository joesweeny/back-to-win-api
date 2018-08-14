<?php

namespace BackToWin\Domain\GameEntry;

use BackToWin\Domain\Game\Orchestrator as GameOrchestrator;
use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\Persistence\Repository;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Domain\UserPurse\Orchestrator as PurseOrchestrator;
use BackToWin\Framework\Calculation\Calculation;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;
use Money\Money;

class Orchestrator
{
    /**
     * @var GameOrchestrator
     */
    private $gameOrchestrator;
    /**
     * @var UserOrchestrator
     */
    private $userOrchestrator;
    /**
     * @var GameManager
     */
    private $manager;
    /**
     * @var PurseOrchestrator
     */
    private $purseOrchestrator;
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(
        GameOrchestrator $gameOrchestrator,
        UserOrchestrator $userOrchestrator,
        PurseOrchestrator $purseOrchestrator,
        GameManager $manager,
        Repository $repository
    ) {
        $this->gameOrchestrator = $gameOrchestrator;
        $this->userOrchestrator = $userOrchestrator;
        $this->manager = $manager;
        $this->purseOrchestrator = $purseOrchestrator;
        $this->repository = $repository;
    }

    /**
     * Add a User to a Game and update their UserPurse to reflect the deduction of Game entry fee
     *
     * @param Uuid $gameId
     * @param Uuid $userId
     * @throws NotFoundException
     * @throws GameEntryException
     * @return void
     */
    public function enterGame(Uuid $gameId, Uuid $userId): void
    {
        $game = $this->gameOrchestrator->getGameById($gameId);

        if ($game->getStatus()->getValue() !== 'CREATED') {
            throw new GameEntryException("Cannot enter Game {$game->getId()} as game status is {$game->getStatus()}");
        }

        $this->manager->addUserToGame($game, $user = $this->userOrchestrator->getUserById($userId));

        $purse = $this->purseOrchestrator->getUserPurse($user->getId());

        $this->purseOrchestrator->createTransaction(
            (new UserPurseTransaction())
                ->setDescription("Game {$game->getId()} entry")
                ->setUserId($user->getId())
                ->setCalculation(Calculation::SUBTRACT())
                ->setTotal($game->getBuyIn())
        );

        $this->purseOrchestrator->updateUserPurse($this->subtractFromPurse($purse, $game->getBuyIn()));
    }

    /**
     * @param Uuid $gameId
     * @return array|User[]
     */
    public function getUsersForGame(Uuid $gameId): array
    {
        return array_map(function (GameEntry $entry) {
            return $this->userOrchestrator->getUserById($entry->getUserId());
        }, $this->repository->get($gameId));
    }

    private function subtractFromPurse(UserPurse $purse, Money $money): UserPurse
    {
        return new UserPurse($purse->getUserId(), $purse->getTotal()->subtract($money));
    }
}
