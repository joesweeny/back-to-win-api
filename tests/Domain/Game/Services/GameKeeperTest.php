<?php

namespace GamePlatform\Domain\Game\Services;

use Cake\Chronos\Chronos;
use GamePlatform\Domain\Admin\Bank\Services\FundsHandler;
use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Enum\GameType;
use GamePlatform\Domain\Game\Exception\GameSettlementException;
use GamePlatform\Domain\Game\GameOrchestrator;
use GamePlatform\Domain\GameEntry\Exception\GameEntryException;
use GamePlatform\Domain\GameEntry\GameEntryOrchestrator;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\User\Services\UserFundsHandler;
use GamePlatform\Framework\DateTime\Clock;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class GameKeeperTest extends TestCase
{
    /** @var  GameEntryOrchestrator */
    private $entryOrchestrator;
    /** @var  UserFundsHandler */
    private $userFundsHandler;
    /** @var  FundsHandler */
    private $adminFundsHandler;
    /** @var  GameKeeper */
    private $keeper;
    /** @var  GameOrchestrator */
    private $gameOrchestrator;
    /** @var  Clock */
    private $clock;

    public function setUp()
    {
        $this->gameOrchestrator = $this->prophesize(GameOrchestrator::class);
        $this->entryOrchestrator = $this->prophesize(GameEntryOrchestrator::class);
        $this->userFundsHandler = $this->prophesize(UserFundsHandler::class);
        $this->adminFundsHandler = $this->prophesize(FundsHandler::class);
        $this->clock = $this->prophesize(Clock::class);
        $this->keeper = new GameKeeper(
            $this->gameOrchestrator->reveal(),
            $this->entryOrchestrator->reveal(),
            $this->userFundsHandler->reveal(),
            $this->adminFundsHandler->reveal(),
            $this->clock->reveal()
        );
    }

    public function test_user_game_entry_is_processed_correctly()
    {
        $user = new User();

        $this->gameOrchestrator->getGameToEnter(new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'))->willReturn(
            $game = $this->createGame(4)
        );

        $this->entryOrchestrator->checkEntryEligibility($game, $user->getId())->shouldBeCalled();

        $this->userFundsHandler->handleGameEntryFee($game, $user)->shouldBeCalled();

        $this->entryOrchestrator->addGameEntry($game, $user)->shouldBeCalled();

        $this->keeper->processUserGameEntry($game->getId(), $user);

        $this->addToAssertionCount(1);
    }

    public function test_exception_is_thrown_if_user_is_not_eligible_to_join_a_game()
    {
        $user = new User();

        $this->gameOrchestrator->getGameToEnter(new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'))->willReturn(
            $game = $this->createGame(4)
        );

        $this->entryOrchestrator->checkEntryEligibility($game, $user->getId())->willThrow(
            $e = new GameEntryException('User is not eligible')
        );

        $this->userFundsHandler->handleGameEntryFee($game, $user)->shouldNotBeCalled();

        $this->entryOrchestrator->addGameEntry($game, $user)->shouldNotBeCalled();

        $this->expectException(GameEntryException::class);

        $this->keeper->processUserGameEntry($game->getId(), $user);
    }

    public function test_exception_is_thrown_if_unable_to_process_funds_to_join_a_game()
    {
        $this->gameOrchestrator->getGameToEnter(new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'))->willReturn(
            $game = $this->createGame(4)
        );

        $user = new User();

        $this->entryOrchestrator->checkEntryEligibility($game, $user->getId())->shouldBeCalled();

        $this->userFundsHandler->handleGameEntryFee($game, $user)->willThrow(
            $e = new GameEntryException('User has no funds man')
        );

        $this->entryOrchestrator->addGameEntry($game, $user)->shouldNotBeCalled();

        $this->expectException(GameEntryException::class);

        $this->keeper->processUserGameEntry($game->getId(), $user);
    }

    public function test_game_settlement_is_processed_correctly()
    {
        $user = new User();

        $this->gameOrchestrator->getGameToSettle(new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'))->willReturn(
            $game = $this->createGame(4)
        );

        $this->clock->now()->willReturn(
            new Chronos('2018-07-19 00:00:00')
        );

        $this->entryOrchestrator->isUserInGame($game, $user->getId())->willReturn(true);

        $this->userFundsHandler->settleGameWinnings(
            $game->getId(), $user->getId(), new Money(50, new Currency('GBP'))
        )->willReturn($remainder = new Money(450, new Currency('GBP')));

        $this->adminFundsHandler->addSettledGameFunds($game->getId(), $remainder)->shouldBeCalled();

        $this->gameOrchestrator->completeGame($game, $user->getId())->shouldBeCalled();

        $this->keeper->processGameSettlement($game->getId(), $user, new Money(50, new Currency('GBP')));
    }

    public function test_exception_is_thrown_if_settling_a_game_with_a_user_who_has_not_entered()
    {
        $user = new User();

        $this->gameOrchestrator->getGameToSettle(new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'))->willReturn(
            $game = $this->createGame(4)
        );

        $this->clock->now()->willReturn(
            new Chronos('2018-07-19 00:00:00')
        );

        $this->entryOrchestrator->isUserInGame($game, $user->getId())->willReturn(false);

        $this->userFundsHandler->settleGameWinnings(
            $game->getId(), $user->getId(), new Money(50, new Currency('GBP'))
        )->shouldNotBeCalled();

        $this->adminFundsHandler->addSettledGameFunds($game->getId(), Argument::type(Money::class))->shouldNotBeCalled();

        $this->gameOrchestrator->completeGame($game, $user->getId())->shouldNotBeCalled();

        $this->expectException(GameSettlementException::class);
        $this->expectExceptionMessage("Unable to settle as User {$user->getId()} did not enter Game {$game->getId()}");
        $this->keeper->processGameSettlement($game->getId(), $user, new Money(50, new Currency('GBP')));
    }

    public function test_exception_is_thrown_if_attempting_to_settle_a_game_that_has_not_started_yet()
    {
        $user = new User();

        $this->gameOrchestrator->getGameToSettle(new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'))->willReturn(
            $game = $this->createGame(4)
        );

        $this->clock->now()->willReturn(
            new Chronos('2018-07-10 00:00:00')
        );

        $this->entryOrchestrator->isUserInGame($game, $user->getId())->shouldNotBeCalled();

        $this->userFundsHandler->settleGameWinnings(
            $game->getId(), $user->getId(), new Money(50, new Currency('GBP'))
        )->shouldNotBeCalled();

        $this->adminFundsHandler->addSettledGameFunds($game->getId(), Argument::type(Money::class))->shouldNotBeCalled();

        $this->gameOrchestrator->completeGame($game, $user->getId())->shouldNotBeCalled();

        $this->expectException(GameSettlementException::class);
        $this->expectExceptionMessage("Cannot settle Game {$game->getId()} as the game has not started yet");
        $this->keeper->processGameSettlement($game->getId(), $user, new Money(50, new Currency('GBP')));
    }

    private function createGame(int $players): Game
    {
        return new Game(
            new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'),
            GameType::GENERAL_KNOWLEDGE(),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP')),
            new Money(50, new Currency('GBP')),
            new Money(10, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            $players
        );
    }
}
