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
            new Money(500, new Currency('GBP')),
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
            new Money(15000, new Currency('GBP')),
            new Money(5000, new Currency('GBP')),
            new Money(100, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            4
        );

        $game2->setCreatedDate(new \DateTimeImmutable('2018-07-25 00:00:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2018-07-25 00:00:00'));

        $this->orchestrator->getGames()->willReturn([$game1, $game2]);

        $games = $this->handler->handle(new ListGamesCommand());

        $this->assertEquals('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2', $games[0]->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $games[0]->type);
        $this->assertEquals('CREATED', $games[0]->status);
        $this->assertEquals('GBP', $games[0]->currency);
        $this->assertEquals(500, $games[0]->buy_in);
        $this->assertEquals(50, $games[0]->max);
        $this->assertEquals(10, $games[0]->min);
        $this->assertEquals(4, $games[0]->players);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $games[0]->start);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $games[0]->created_at);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $games[0]->updated_at);

        $this->assertEquals('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2', $games[1]->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $games[1]->type);
        $this->assertEquals('COMPLETED', $games[1]->status);
        $this->assertEquals('GBP', $games[1]->currency);
        $this->assertEquals(15000, $games[1]->buy_in);
        $this->assertEquals(5000, $games[1]->max);
        $this->assertEquals(100, $games[1]->min);
        $this->assertEquals(4, $games[1]->players);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $games[1]->start);
        $this->assertEquals('2018-07-25T00:00:00+00:00', $games[1]->created_at);
        $this->assertEquals('2018-07-25T00:00:00+00:00', $games[1]->updated_at);
    }
}
