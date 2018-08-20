<?php

namespace GamePlatform\Domain\User\Services;

use GamePlatform\Domain\Bank\BankManager;
use GamePlatform\Domain\Bank\Exception\BankingException;
use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Enum\GameType;
use GamePlatform\Domain\GameEntry\Entity\GameEntry;
use GamePlatform\Domain\GameEntry\Exception\GameEntryException;
use GamePlatform\Domain\GameEntry\Services\EntryFee\EntryFeeStore;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\UserPurse\Entity\UserPurse;
use GamePlatform\Domain\UserPurse\Entity\UserPurseTransaction;
use GamePlatform\Domain\UserPurse\UserPurseOrchestrator;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class UserFundsHandlerTest extends TestCase
{
    /** @var  EntryFeeStore */
    private $feeStore;
    /** @var  UserFundsHandler */
    private $handler;
    /** @var  BankManager */
    private $bankManager;
    /** @var UserPurseOrchestrator */
    private $purseOrchestrator;

    public function setUp()
    {
        $this->bankManager = $this->prophesize(BankManager::class);
        $this->feeStore = $this->prophesize(EntryFeeStore::class);
        $this->purseOrchestrator = $this->prophesize(UserPurseOrchestrator::class);
        $this->handler = new UserFundsHandler(
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

        $this->purseOrchestrator->getUserPurse($user->getId())->willReturn(
            $purse = new UserPurse($user->getId(), new Money(1000, new Currency('GBP')))
        );

        $this->purseOrchestrator->updateUserPurse($purse->subtractMoney($entryFee))->shouldBeCalled();

        $this->purseOrchestrator->createTransaction(Argument::type(UserPurseTransaction::class))->shouldBeCalled();

        $this->handler->handleGameEntryFee($game, $user);
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

        $this->handler->handleGameEntryFee($game, $user);
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
