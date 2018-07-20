<?php

namespace BackToWin\Domain\Game\Persistence\Illuminate;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\Game\Persistence\GameRepositoryQuery;
use BackToWin\Domain\Game\Persistence\Reader;
use BackToWin\Domain\Game\Persistence\Writer;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\RunsMigrations;
use BackToWin\Testing\Traits\UsesContainer;
use Interop\Container\ContainerInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class IlluminateReaderIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Writer */
    private $writer;
    /** @var  Reader */
    private $reader;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->writer = $this->container->get(Writer::class);
        $this->reader = $this->container->get(Reader::class);
    }

    public function test_interface_is_bound()
    {
        $this->assertInstanceOf(Reader::class, $this->reader);
    }

    public function test_game_can_be_retrieved_by_id()
    {
        $this->writer->insert(
            new Game(
                new Uuid('7967168d-6608-4397-b24d-9e02b5426269'),
                GameType::WINNER_TAKES_ALL(),
                GameStatus::CREATED(),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $fetched = $this->reader->getById(new Uuid('7967168d-6608-4397-b24d-9e02b5426269'));

        $this->assertEquals('7967168d-6608-4397-b24d-9e02b5426269', $fetched->getId());
        $this->assertEquals(GameType::WINNER_TAKES_ALL(), $fetched->getType());
        $this->assertEquals(GameStatus::CREATED(), $fetched->getStatus());
        $this->assertEquals(new Money(50, new Currency('GBP')), $fetched->getMax());
        $this->assertEquals(new Money(10, new Currency('GBP')), $fetched->getMin());
        $this->assertEquals(new \DateTimeImmutable('2018-07-18 00:00:00'), $fetched->getStartDateTime());
        $this->assertEquals(4, $fetched->getPlayers());
    }

    public function test_exception_is_thrown_if_attempting_to_retrieve_a_game_that_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Unable to retrieve Game 7967168d-6608-4397-b24d-9e02b5426269 as it does not exist');
        $this->reader->getById(new Uuid('7967168d-6608-4397-b24d-9e02b5426269'));
    }

    public function test_game_retrieval_can_be_filtered_by_status()
    {
        $this->writer->insert(
            new Game(
                Uuid::generate(),
                GameType::WINNER_TAKES_ALL(),
                GameStatus::CREATED(),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $this->writer->insert(
            new Game(
                Uuid::generate(),
                GameType::WINNER_TAKES_ALL(),
                GameStatus::CANCELLED(),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $this->writer->insert(
            new Game(
                Uuid::generate(),
                GameType::WINNER_TAKES_ALL(),
                GameStatus::CREATED(),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $games = $this->reader->get((new GameRepositoryQuery())->whereStatusEquals(GameStatus::CREATED()));

        $this->assertCount(2, $games);

        foreach ($games as $game) {
            $this->assertEquals(GameStatus::CREATED(), $game->getStatus());
        }
    }

    public function test_all_game_records_are_returned_if_no_query_is_provided_to_argument()
    {
        $this->writer->insert(
            new Game(
                Uuid::generate(),
                GameType::WINNER_TAKES_ALL(),
                GameStatus::CREATED(),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $this->writer->insert(
            new Game(
                Uuid::generate(),
                GameType::WINNER_TAKES_ALL(),
                GameStatus::CANCELLED(),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $this->writer->insert(
            new Game(
                Uuid::generate(),
                GameType::WINNER_TAKES_ALL(),
                GameStatus::CREATED(),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $games = $this->reader->get();

        $this->assertCount(3, $games);
    }

    public function test_empty_array_is_returned_if_no_records_are_in_database()
    {
        $this->assertEmpty($this->reader->get());
    }
}
