<?php

namespace BackToWin\Domain\Game\Services;

use BackToWin\Domain\Admin\Bank\Services\FundsHandler;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Domain\GameEntry\GameEntryOrchestrator;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\Services\UserFundsHandler;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

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

    public function setUp()
    {
        $this->entryOrchestrator = $this->prophesize(GameEntryOrchestrator::class);
        $this->userFundsHandler = $this->prophesize(UserFundsHandler::class);
        $this->adminFundsHandler = $this->prophesize(FundsHandler::class);
        $this->keeper = new GameKeeper(
            $this->entryOrchestrator->reveal(),
            $this->userFundsHandler->reveal(),
            $this->adminFundsHandler->reveal()
        );
    }

    public function test_user_game_entry_is_processed_correctly()
    {
        $game = $this->createGame(4);

        $user = new User();

        $this->entryOrchestrator->checkEntryEligibility($game, $user->getId())->shouldBeCalled();

        $this->userFundsHandler->handleGameEntryFee($game, $user)->shouldBeCalled();

        $this->entryOrchestrator->addGameEntry($game, $user)->shouldBeCalled();

        $this->keeper->processUserGameEntry($game, $user);

        $this->addToAssertionCount(1);
    }

    public function test_exception_is_thrown_if_user_is_not_eligible_to_join_a_game()
    {
        $game = $this->createGame(4);

        $user = new User();

        $this->entryOrchestrator->checkEntryEligibility($game, $user->getId())->willThrow(
            $e = new GameEntryException('User is not eligible')
        );

        $this->userFundsHandler->handleGameEntryFee($game, $user)->shouldNotBeCalled();

        $this->entryOrchestrator->addGameEntry($game, $user)->shouldNotBeCalled();

        $this->expectException(GameEntryException::class);

        $this->keeper->processUserGameEntry($game, $user);
    }

    public function test_exception_is_thrown_if_unable_to_process_funds_to_join_a_game()
    {
        $game = $this->createGame(4);

        $user = new User();

        $this->entryOrchestrator->checkEntryEligibility($game, $user->getId())->shouldBeCalled();

        $this->userFundsHandler->handleGameEntryFee($game, $user)->willThrow(
            $e = new GameEntryException('User has no funds man')
        );

        $this->entryOrchestrator->addGameEntry($game, $user)->shouldNotBeCalled();

        $this->expectException(GameEntryException::class);

        $this->keeper->processUserGameEntry($game, $user);
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
