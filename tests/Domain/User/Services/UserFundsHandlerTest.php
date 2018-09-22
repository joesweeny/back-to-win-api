<?php

namespace BackToWin\Domain\User\Services;

use BackToWin\Domain\Bank\BankManager;
use BackToWin\Domain\Bank\Exception\BankingException;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\Services\EntryFee\EntryFeeStore;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Domain\UserPurse\UserPurseOrchestrator;
use BackToWin\Framework\Uuid\Uuid;
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

        $this->bankManager->getBalance($user->getId())->willReturn(new Money(1000, new Currency('GBP')));

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

    public function test_exception_is_thrown_if_issue_with_user_bank_currency_differ_from_game_buy_in_currency()
    {
        $game = $this->createGame();

        $user = new User('57f08f28-dc80-4adb-bc6b-1cfff1b73d6c');

        $this->bankManager->getBalance($user->getId())->willReturn(new Money(1000, new Currency('EUR')));

        $this->bankManager->withdraw($user, $game->getBuyIn())->shouldNotBeCalled();

        $this->feeStore->enter(Argument::type(GameEntry::class), Argument::type(Money::class))->shouldNotBeCalled();

        $this->purseOrchestrator->createTransaction(Argument::type(UserPurseTransaction::class))->shouldNotBeCalled();


        $this->expectException(GameEntryException::class);
        $this->expectExceptionMessage('User cannot enter game due to Game currency and user bank currency mismatch');

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
