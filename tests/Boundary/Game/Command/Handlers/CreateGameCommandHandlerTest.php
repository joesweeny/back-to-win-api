<?php

namespace BackToWin\Boundary\Game\Command\Handlers;

use BackToWin\Boundary\Game\Command\CreateGameCommand;
use BackToWin\Boundary\Game\GamePresenter;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\Game\Orchestrator;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CreateGameCommandHandlerTest extends TestCase
{
    /** @var  Orchestrator */
    private $orchestrator;
    /** @var  CreateGameCommandHandler */
    private $handler;
    /** @var  GamePresenter */
    private $presenter;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(Orchestrator::class);
        $this->presenter = $this->prophesize(GamePresenter::class);
        $this->handler = new CreateGameCommandHandler(
            $this->orchestrator->reveal(),
            $this->presenter->reveal()
        );
    }

    /**
     *
     */
    public function test_handle_creates_a_new_game_and_returns_a_scalar_object_containing_game_data()
    {
        $command = new CreateGameCommand(
            'GENERAL_KNOWLEDGE',
            'CREATED',
            'GBP',
            50,
            10,
            '2018-07-18T00:00:00+00:00',
            4
        );

        $this->orchestrator->createGame(Argument::that(function (Game $game) {
            $this->assertEquals(GameType::GENERAL_KNOWLEDGE(), $game->getType());
            $this->assertEquals(GameStatus::CREATED(), $game->getStatus());
            $this->assertEquals(new Money(50, new Currency('GBP')), $game->getMax());
            $this->assertEquals(new Money(10, new Currency('GBP')), $game->getMin());
            $this->assertEquals(4, $game->getPlayers());
            return true;
        }))->shouldBeCalled();

        $this->presenter->toDto(Argument::type(Game::class))->shouldBeCalled();

        $this->handler->handle($command);
    }
}
