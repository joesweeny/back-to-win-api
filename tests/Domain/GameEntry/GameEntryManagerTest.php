<?php

namespace BackToWin\Domain\GameEntry;

use BackToWin\Domain\Bank\BankManager;
use BackToWin\Domain\Bank\Exception\BankingException;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\Services\EntryFee\EntryFeeStore;
use BackToWin\Domain\GameEntry\Services\GameEntryManager;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Domain\UserPurse\UserPurseOrchestrator;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class GameEntryManagerTest extends TestCase
{
    /** @var  EntryFeeStore */
    private $feeStore;
    /** @var  GameEntryManager */
    private $manager;
    /** @var  BankManager */
    private $bankManager;
    /** @var UserPurseOrchestrator */
    private $purseOrchestrator;

    public function setUp()
    {
        $this->bankManager = $this->prophesize(BankManager::class);
        $this->feeStore = $this->prophesize(EntryFeeStore::class);
        $this->purseOrchestrator = $this->prophesize(UserPurseOrchestrator::class);
        $this->manager = new GameEntryManager(
            $this->bankManager->reveal(),
            $this->feeStore->reveal(),
            $this->purseOrchestrator->reveal()
        );
    }

    public function test_user_is_added_to_game_if_user_has_enough_funds()
    {
        $game = $this->createGame();

        $user = new User('57f08f28-dc80-4adb-bc6b-1cfff1b73d6c');

        $this->bankManager->withdraw($user, $game->getBuyIn())->willReturn(
            $entryFee = new Money(500, new Currency('GBP'))
        );

        $this->feeStore->enter(
            new GameEntry($game->getId(), $user->getId()),
            $entryFee
        )->shouldBeCalled();

        $this->purseOrchestrator->createTransaction(Argument::type(UserPurseTransaction::class))->shouldBeCalled();

        $this->manager->handleGameEntryFee($game, $user);
    }

    public function test_exception_is_thrown_if_issue_with_withdrawing_funds()
    {
        $game = $this->createGame();

        $user = new User('57f08f28-dc80-4adb-bc6b-1cfff1b73d6c');

        $this->bankManager->withdraw($user, $game->getBuyIn())->willThrow(
            $e = new BankingException('No funds mate')
        );

        $this->feeStore->enter(Argument::type(GameEntry::class), Argument::type(Money::class))->shouldNotBeCalled();

        $this->purseOrchestrator->createTransaction(Argument::type(UserPurseTransaction::class))->shouldNotBeCalled();


        $this->expectException(GameEntryException::class);
        $this->expectExceptionMessage(
            "User {$user->getId()} cannot enter Game {$game->getId()}. Message: {$e->getMessage()}"
        );

        $this->manager->handleGameEntryFee($game, $user);
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
