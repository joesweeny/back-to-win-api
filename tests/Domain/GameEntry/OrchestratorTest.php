<?php

namespace BackToWin\Domain\GameEntry;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class OrchestratorTest extends TestCase
{
    /** @var  Orchestrator */
    private $orchestrator;
    /** @var  \BackToWin\Domain\Game\Orchestrator */
    private $gameOrchestrator;
    /** @var  UserOrchestrator */
    private $userOrchestrator;
    /** @var  \BackToWin\Domain\UserPurse\Orchestrator */
    private $purseOrchestrator;
    /** @var  GameManager */
    private $manager;

    public function setUp()
    {
        $this->gameOrchestrator = $this->prophesize(\BackToWin\Domain\Game\Orchestrator::class);
        $this->userOrchestrator = $this->prophesize(UserOrchestrator::class);
        $this->purseOrchestrator = $this->prophesize(\BackToWin\Domain\UserPurse\Orchestrator::class);
        $this->manager = $this->prophesize(GameManager::class);
        $this->orchestrator = new Orchestrator(
            $this->gameOrchestrator->reveal(),
            $this->userOrchestrator->reveal(),
            $this->purseOrchestrator->reveal(),
            $this->manager->reveal()
        );
    }

    public function test_enter_game_adds_user_to_game_and_adds_user_purse_transaction_and_updates_user_purse()
    {
        $this->gameOrchestrator->getGameById(new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'))->willReturn(
            $game = new Game(
                new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'),
                GameType::GENERAL_KNOWLEDGE(),
                GameStatus::CREATED(),
                new Money(500, new Currency('GBP')),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $this->userOrchestrator->getUserById(new Uuid('8e82645d-8093-4448-908c-e085e7e7357d'))->willReturn(
            $user = new User('8e82645d-8093-4448-908c-e085e7e7357d')
        );

        $this->manager->addUserToGame($game, $user)->shouldBeCalled();

        $this->purseOrchestrator->getUserPurse($user->getId())->willReturn(
            new UserPurse($user->getId(), new Money(1000, new Currency('GBP')))
        );

        $this->purseOrchestrator->createTransaction(Argument::type(UserPurseTransaction::class))->shouldBeCalled();

        $this->purseOrchestrator->updateUserPurse(
            new UserPurse($user->getId(), new Money(500, new Currency('GBP')))
        )->shouldBeCalled();

        $this->orchestrator->enterGame($game->getId(), $user->getId());
    }

    public function test_exception_is_thrown_if_game_status_is_not_created()
    {
        $this->gameOrchestrator->getGameById(new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'))->willReturn(
            $game = new Game(
                new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'),
                GameType::GENERAL_KNOWLEDGE(),
                GameStatus::COMPLETED(),
                new Money(500, new Currency('GBP')),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $this->expectException(GameEntryException::class);
        $this->expectExceptionMessage("Cannot enter Game {$game->getId()} as game status is {$game->getStatus()}");

        $this->orchestrator->enterGame($game->getId(), Uuid::generate());
    }

    public function test_user_purse_is_not_updated_if_game_manager_fails_to_add_user_to_game()
    {
        $this->gameOrchestrator->getGameById(new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'))->willReturn(
            $game = new Game(
                new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'),
                GameType::GENERAL_KNOWLEDGE(),
                GameStatus::CREATED(),
                new Money(500, new Currency('GBP')),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $this->userOrchestrator->getUserById(new Uuid('8e82645d-8093-4448-908c-e085e7e7357d'))->willReturn(
            $user = new User('8e82645d-8093-4448-908c-e085e7e7357d')
        );

        $this->manager->addUserToGame($game, $user)->willThrow($e = new GameEntryException('Cannot enter game'));

        $this->purseOrchestrator->getUserPurse($user->getId())->shouldNotBeCalled();

        $this->purseOrchestrator->createTransaction(Argument::type(UserPurseTransaction::class))->shouldNotBeCalled();

        $this->purseOrchestrator->updateUserPurse(Argument::type(UserPurse::class))->shouldNotBeCalled();

        $this->expectException(GameEntryException::class);
        $this->orchestrator->enterGame($game->getId(), $user->getId());
    }
}
