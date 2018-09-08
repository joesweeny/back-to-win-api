<?php

namespace GamePlatform\Domain\GameEntry;

use Cake\Chronos\Chronos;
use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Enum\GameType;
use GamePlatform\Domain\GameEntry\Entity\GameEntry;
use GamePlatform\Domain\GameEntry\Exception\GameEntryException;
use GamePlatform\Domain\GameEntry\Persistence\Repository;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Framework\DateTime\Clock;
use GamePlatform\Framework\Uuid\Uuid;
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
    /** @var  Clock */
    private $clock;

    public function setUp()
    {
        $this->repository = $this->prophesize(Repository::class);
        $this->userOrchestrator = $this->prophesize(UserOrchestrator::class);
        $this->clock = $this->prophesize(Clock::class);
        $this->orchestrator = new GameEntryOrchestrator(
            $this->repository->reveal(),
            $this->userOrchestrator->reveal(),
            $this->clock->reveal()
        );
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

        $this->expectException(GameEntryException::class);
        $this->expectExceptionMessage('Game has reached full capacity');
        $this->orchestrator->checkEntryEligibility($game, (new User())->getId());
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

        $this->clock->now()->willReturn(new Chronos('2018-07-10 00:00:00'));

        $this->repository->get($game->getId())->willReturn([
            new GameEntry($game->getId(), $userId = Uuid::generate()),
            new GameEntry($game->getId(), Uuid::generate())
        ]);

        $this->expectException(GameEntryException::class);
        $this->expectExceptionMessage('User has already entered Game');
        $this->orchestrator->checkEntryEligibility($game, $userId);
    }

    public function test_exception_is_thrown_if_game_has_already_started()
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

        $this->clock->now()->willReturn(new Chronos('2018-07-19 00:00:00'));

        $this->repository->get($game->getId())->willReturn([
            new GameEntry($game->getId(), $userId = Uuid::generate()),
            new GameEntry($game->getId(), Uuid::generate())
        ]);

        $this->expectException(GameEntryException::class);
        $this->expectExceptionMessage('Game has already started');
        $this->orchestrator->checkEntryEligibility($game, $userId);
    }

    public function test_exception_is_thrown_if_game_does_not_have_correct_status_to_enter()
    {
        $game = new Game(
            new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'),
            GameType::GENERAL_KNOWLEDGE(),
            GameStatus::COMPLETED(),
            new Money(500, new Currency('GBP')),
            new Money(50, new Currency('GBP')),
            new Money(10, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            4
        );

        $this->clock->now()->willReturn(new Chronos('2018-07-10 00:00:00'));

        $this->repository->get($game->getId())->willReturn([
            new GameEntry($game->getId(), $userId = Uuid::generate()),
            new GameEntry($game->getId(), Uuid::generate())
        ]);

        $this->expectException(GameEntryException::class);
        $this->expectExceptionMessage('Game does not have the correct status to enter');
        $this->orchestrator->checkEntryEligibility($game, $userId);
    }
}
