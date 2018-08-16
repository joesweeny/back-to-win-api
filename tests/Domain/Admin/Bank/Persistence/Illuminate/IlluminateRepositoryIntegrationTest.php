<?php

namespace BackToWin\Domain\Admin\Bank\Persistence\Illuminate;

use BackToWin\Domain\Admin\Bank\Exception\RepositoryDuplicationException;
use BackToWin\Domain\Admin\Bank\Persistence\Repository;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\RunsMigrations;
use BackToWin\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class IlluminateRepositoryIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Repository */
    private $repository;
    /** @var  Connection */
    private $connection;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->repository = $this->container->get(Repository::class);
        $this->connection = $this->container->get(Connection::class);
    }

    public function test_interface_is_bound()
    {
        $this->assertInstanceOf(Repository::class, $this->repository);
    }

    public function test_insert_adds_to_table_count()
    {
        for ($i = 0; $i < 4; $i++) {
            $this->repository->insert(Uuid::generate(), new Money(1000, new Currency('GBP')));
        }

        $total = $this->connection->table('admin_bank_transaction')->get();

        $this->assertCount(4, $total);
    }

    public function test_exception_is_thrown_if_record_for_game_id_already_exists()
    {
        $this->repository->insert($id = Uuid::generate(), new Money(1000, new Currency('GBP')));

        $this->expectException(RepositoryDuplicationException::class);
        $this->expectExceptionMessage("Record for Game {$id} already exists");

        $this->repository->insert($id, new Money(1000, new Currency('GBP')));
    }
}
