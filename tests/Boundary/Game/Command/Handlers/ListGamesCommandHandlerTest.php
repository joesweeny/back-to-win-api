<?php

namespace BackToWin\Boundary\Game\Command\Handlers;

use BackToWin\Boundary\Game\Command\ListGamesCommand;
use BackToWin\Boundary\Game\GamePresenter;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\Game\Orchestrator;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class ListGamesCommandHandlerTest extends TestCase
{
    /** @var  Orchestrator */
    private $orchestrator;
    /** @var  ListGamesCommandHandler */
    private $handler;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(Orchestrator::class);
        $this->handler = new ListGamesCommandHandler(
            $this->orchestrator->reveal(),
            new GamePresenter()
        );
    }

    public function test_handle_returns_an_array_of_scalar_objects_containing_game_data()
    {
        $game1 = new Game(
            new Uuid('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2'),
            GameType::GENERAL_KNOWLEDGE(),
            GameStatus::CREATED(),
            new Money(50, new Currency('GBP')),
            new Money(10, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            4
        );

        $game1->setCreatedDate(new \DateTimeImmutable('2018-07-18 00:00:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2018-07-18 00:00:00'));

        $game2 = new Game(
            new Uuid('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2'),
            GameType::GENERAL_KNOWLEDGE(),
            GameStatus::COMPLETED(),
            new Money(5000, new Currency('GBP')),
            new Money(100, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            4
        );

        $game2->setCreatedDate(new \DateTimeImmutable('2018-07-25 00:00:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2018-07-25 00:00:00'));

        $this->orchestrator->getGames()->willReturn([$game1, $game2]);

        $games = $this->handler->handle(new ListGamesCommand());

        dd($games);
    }
}
