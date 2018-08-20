<?php

namespace GamePlatform\Domain\GameResult\Persistence\Illuminate;

use GamePlatform\Domain\GameResult\Persistence\Repository;
use GamePlatform\Framework\Exception\RepositoryDuplicationException;
use GamePlatform\Framework\Uuid\Uuid;
use GamePlatform\Testing\Traits\RunsMigrations;
use GamePlatform\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
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

    public function test_insert_increases_table_count()
    {
        for ($i = 0; $i < 4; $i++) {
            $this->repository->insert(Uuid::generate(), Uuid::generate());
        }

        $total = $this->connection->table('game_result')->get();

        $this->assertCount(4, $total);
    }

    public function test_exception_is_thrown_if_attempting_to_insert_a_record_for_a_game_that_already_exists()
    {
        $this->repository->insert($gameId = Uuid::generate(), Uuid::generate());

        $this->expectException(RepositoryDuplicationException::class);
        $this->expectExceptionMessage("Game result for Game {$gameId} already exists");

        $this->repository->insert($gameId, Uuid::generate());
    }
}
