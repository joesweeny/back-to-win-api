<?php

namespace BackToWin\Domain\Game\Persistence\Illuminate;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\Game\Persistence\Writer;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\RunsMigrations;
use BackToWin\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class IlluminateWriterIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Writer */
    private $writer;
    /** @var  Connection */
    private $connection;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->writer = $this->container->get(Writer::class);
        $this->connection = $this->container->get(Connection::class);
    }

    public function test_interface_is_bound()
    {
        $this->assertInstanceOf(Writer::class, $this->writer);
    }

    public function test_insert_increases_table_count()
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

        $total = $this->connection->table('game')->get();

        $this->assertCount(1, $total);

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

        $total = $this->connection->table('game')->get();

        $this->assertCount(2, $total);
    }

    public function test_a_game_record_can_be_updated()
    {
        $this->writer->insert(
            new Game(
                $id = Uuid::generate(),
                GameType::WINNER_TAKES_ALL(),
                GameStatus::CREATED(),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $fetched = $this->connection->table('game')->where('id', $id->toBinary())->first();

        $this->assertEquals('WINNER_TAKES_ALL', $fetched->type);
        $this->assertEquals('CREATED', $fetched->status);
        $this->assertEquals(50, $fetched->max);
        $this->assertEquals(1531872000, $fetched->start);
        $this->assertEquals(4, $fetched->players);

        $this->writer->update(
            (new Game(
                $id,
                GameType::WINNER_TAKES_ALL(),
                GameStatus::COMPLETED(),
                new Money(500, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                2
            ))->setCreatedDate(new \DateTimeImmutable())
        );

        $fetched = $this->connection->table('game')->where('id', $id->toBinary())->first();

        $this->assertEquals('WINNER_TAKES_ALL', $fetched->type);
        $this->assertEquals('COMPLETED', $fetched->status);
        $this->assertEquals(500, $fetched->max);
        $this->assertEquals(1531872000, $fetched->start);
        $this->assertEquals(2, $fetched->players);
    }

    public function test_exception_is_thrown_if_attempting_to_update_a_game_that_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Unable to update Game 7967168d-6608-4397-b24d-9e02b5426269 as it does not exist');
        $this->writer->update(
            (new Game(
                new Uuid('7967168d-6608-4397-b24d-9e02b5426269'),
                GameType::WINNER_TAKES_ALL(),
                GameStatus::COMPLETED(),
                new Money(500, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            ))->setCreatedDate(new \DateTimeImmutable())
        );
    }
}
