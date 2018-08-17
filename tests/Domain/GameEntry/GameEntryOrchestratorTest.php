<?php

namespace BackToWin\Domain\GameEntry;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\Persistence\Repository;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class GameEntryOrchestratorTest extends TestCase
{
    /** @var  GameEntryOrchestrator */
    private $orchestrator;
    /** @var  Repository */
    private $repository;
    /** @var  UserOrchestrator */
    private $userOrchestrator;

    public function setUp()
    {
        $this->repository = $this->prophesize(Repository::class);
        $this->userOrchestrator = $this->prophesize(UserOrchestrator::class);
        $this->orchestrator = new GameEntryOrchestrator(
            $this->repository->reveal(),
            $this->userOrchestrator->reveal()
        );
    }

    public function test_add_game_entry_checks_game_capacity_and_current_entries_before_adding_user_to_game()
    {
        $game = new Game(
            new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'),
            GameType::GENERAL_KNOWLEDGE(),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP')),
            new Money(50, new Currency('GBP')),
            new Money(10, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            4
        );

        $this->repository->get($game->getId())->willReturn([
           new GameEntry($game->getId(), Uuid::generate()),
           new GameEntry($game->getId(), Uuid::generate())
        ]);

        $this->repository->insert($game->getId(), $id = Uuid::generate())->shouldBeCalled();

        $this->orchestrator->addGameEntry($game, $id);
    }

    public function test_exception_is_thrown_if_game_has_reached_capacity()
    {
        $game = new Game(
            new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'),
            GameType::GENERAL_KNOWLEDGE(),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP')),
            new Money(50, new Currency('GBP')),
            new Money(10, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            2
        );

        $this->repository->get($game->getId())->willReturn([
            new GameEntry($game->getId(), Uuid::generate()),
            new GameEntry($game->getId(), Uuid::generate())
        ]);

        $this->repository->insert($game->getId(), $id = Uuid::generate())->shouldNotBeCalled();

        $this->expectException(GameEntryException::class);
        $this->expectExceptionMessage('Game has reached full capacity');
        $this->orchestrator->addGameEntry($game, $id);
    }

    public function test_exception_is_thrown_if_user_has_already_entered_the_game()
    {
        $game = new Game(
            new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'),
            GameType::GENERAL_KNOWLEDGE(),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP')),
            new Money(50, new Currency('GBP')),
            new Money(10, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            4
        );

        $this->repository->get($game->getId())->willReturn([
            new GameEntry($game->getId(), $userId = Uuid::generate()),
            new GameEntry($game->getId(), Uuid::generate())
        ]);

        $this->repository->insert($game->getId(), $id = Uuid::generate())->shouldNotBeCalled();

        $this->expectException(GameEntryException::class);
        $this->expectExceptionMessage('User has already entered Game');
        $this->orchestrator->addGameEntry($game, $userId);
    }
}
