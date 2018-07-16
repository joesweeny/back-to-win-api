<?php

namespace BackToWin\Domain\UserPurse\Persistence\Illuminate;

use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Domain\UserPurse\Persistence\Writer;
use BackToWin\Framework\Calculation\Calculation;
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
            (new UserPurse())
                ->setUserId(Uuid::generate())
                ->setTotal(new Money(500, new Currency('GBP')))
        );

        $total = $this->connection->table('user_purse')->get();

        $this->assertCount(1, $total);

        $this->writer->insert(
            (new UserPurse())
                ->setUserId(Uuid::generate())
                ->setTotal(new Money(500, new Currency('GBP')))
        );

        $total = $this->connection->table('user_purse')->get();

        $this->assertCount(2, $total);
    }

    public function test_update_updates_an_existing_record_in_the_database()
    {
        $this->writer->insert(
            (new UserPurse())
                ->setUserId($id = Uuid::generate())
                ->setTotal(new Money(500, new Currency('GBP')))
        );

        $purse = $this->connection->table('user_purse')->where('user_id', $id->toBinary())->first();

        $this->assertEquals($id, Uuid::createFromBinary($purse->user_id));
        $this->assertEquals(500, $purse->amount);
        $this->assertEquals('GBP', $purse->currency);

        $this->writer->update(
            (new UserPurse())
                ->setUserId($id)
                ->setTotal(new Money(2500, new Currency('GBP')))
                ->setCreatedDate(\DateTimeImmutable::createFromFormat('U', $purse->created_at))
        );

        $purse = $this->connection->table('user_purse')->where('user_id', $id->toBinary())->first();

        $this->assertEquals($id, Uuid::createFromBinary($purse->user_id));
        $this->assertEquals(2500, $purse->amount);
        $this->assertEquals('GBP', $purse->currency);
    }

    public function test_insert_transaction_increase_table_count()
    {
        $this->writer->insertTransaction(
            (new UserPurseTransaction(Uuid::generate()))
                ->setUserId(new Uuid('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21'))
                ->setTotal(new Money(500, new Currency('GBP')))
                ->setCalculation(Calculation::ADD())
                ->setDescription('Payment to customer')
        );

        $total = $this->connection->table('user_purse_transaction')->get();

        $this->assertCount(1, $total);

        $this->writer->insertTransaction(
            (new UserPurseTransaction(Uuid::generate()))
                ->setUserId(new Uuid('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21'))
                ->setTotal(new Money(500, new Currency('GBP')))
                ->setCalculation(Calculation::ADD())
                ->setDescription('Payment to customer')
        );

        $total = $this->connection->table('user_purse_transaction')->get();

        $this->assertCount(2, $total);
    }
}
