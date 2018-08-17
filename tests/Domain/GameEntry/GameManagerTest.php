<?php

namespace BackToWin\Domain\GameEntry;

use BackToWin\Domain\Bank\BankManager;
use BackToWin\Domain\Bank\Exception\BankingException;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\Persistence\Repository;
use BackToWin\Domain\GameEntry\Services\EntryFeeStore;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class GameManagerTest extends TestCase
{
    /** @var  Repository */
    private $repository;
    /** @var  EntryFeeStore */
    private $feeStore;
    /** @var  GameEntryManager */
    private $manager;
    /** @var  BankManager */
    private $bankManager;

    public function setUp()
    {
        $this->repository = $this->prophesize(Repository::class);
        $this->bankManager = $this->prophesize(BankManager::class);
        $this->feeStore = $this->prophesize(EntryFeeStore::class);
        $this->manager = new GameEntryManager(
            $this->repository->reveal(),
            $this->bankManager->reveal(),
            $this->feeStore->reveal()
        );
    }

    public function test_user_is_added_to_game_if_entry_limit_has_not_been_reached_and_user_has_enough_funds()
    {
        $game = $this->createGame();

        $user = new User('57f08f28-dc80-4adb-bc6b-1cfff1b73d6c');

        $this->repository->get($game->getId())->willReturn(['Game', 'Game']);

        $this->bankManager->withdraw($user, $game->getBuyIn())->willReturn(
            $entryFee = new Money(500, new Currency('GBP'))
        );

        $this->repository->insert($game->getId(), $user->getId())->willReturn(
            $entry = new GameEntry($game->getId(), $user->getId())
        );

        $this->feeStore->enter($entry, $entryFee)->shouldBeCalled();

        $this->manager->addUserToGame($game, $user);
    }

    public function test_exception_is_thrown_if_game_has_reached_capacity()
    {
        $game = $this->createGame();

        $user = new User('57f08f28-dc80-4adb-bc6b-1cfff1b73d6c');

        $this->repository->get($game->getId())->willReturn(['Game', 'Game', 'Game', 'Game', 'Game']);

        $this->expectException(GameEntryException::class);
        $this->expectExceptionMessage('Game a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2 has reached full capacity');

        $this->manager->addUserToGame($game, $user);
    }

    public function test_exception_is_thrown_if_issue_with_withdrawing_funds()
    {
        $game = $this->createGame();

        $user = new User('57f08f28-dc80-4adb-bc6b-1cfff1b73d6c');

        $this->repository->get($game->getId())->willReturn(['Game', 'Game']);

        $this->bankManager->withdraw($user, $game->getBuyIn())->willThrow(
            $e = new BankingException('No funds mate')
        );

        $this->repository->insert($game->getId(), $user->getId())->shouldNotBeCalled();

        $this->feeStore->enter(Argument::type(GameEntry::class), Argument::type(Money::class))->shouldNotBeCalled();

        $this->expectException(GameEntryException::class);
        $this->expectExceptionMessage(
            "User {$user->getId()} cannot enter Game {$game->getId()}. Message: {$e->getMessage()}"
        );

        $this->manager->addUserToGame($game, $user);
    }

    private function createGame(): Game
    {
        return new Game(
            new Uuid('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2'),
            GameType::GENERAL_KNOWLEDGE(),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP')),
            new Money(50, new Currency('GBP')),
            new Money(10, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            4
        );
    }
}
