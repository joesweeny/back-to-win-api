<?php

namespace GamePlatform\Domain\Avatar\Persistence\Illuminate;

use GamePlatform\Domain\Avatar\Entity\Avatar;
use GamePlatform\Domain\Avatar\Persistence\Repository;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Uuid\Uuid;
use GamePlatform\Testing\Traits\RunsMigrations;
use GamePlatform\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class IlluminateDbRepositoryIntegrationTest extends TestCase
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
        $this->repository->insert(new Avatar(Uuid::generate(), 'filename.jpg'));

        $total = $this->connection->table('avatar')->get();

        $this->assertCount(1, $total);

        $this->repository->insert(new Avatar(Uuid::generate(), 'filename.jpg'));

        $total = $this->connection->table('avatar')->get();

        $this->assertCount(2, $total);
    }

    public function test_avatar_can_be_retrieved_by_user_id()
    {
        $this->repository->insert($avatar = new Avatar($id = Uuid::generate(), 'filename.jpg'));

        $fetched = $this->repository->get($id);

        $this->assertEquals($avatar->getUserId(), $fetched->getUserId());
        $this->assertEquals('filename.jpg', $fetched->getFilename());
    }

    public function test_exception_is_thrown_if_attempting_to_retrieve_an_avatar_that_does_not_exist()
    {
        $id = Uuid::generate();
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Avatar with User ID {$id} does not exist");
        $this->repository->get($id);
    }

    public function test_an_existing_avatar_can_be_updated()
    {
        $this->repository->insert($avatar = new Avatar($id = Uuid::generate(), 'filename.jpg'));

        $fetched1 = $this->repository->get($id);

        $this->assertEquals($avatar->getUserId(), $fetched1->getUserId());
        $this->assertEquals('filename.jpg', $fetched1->getFilename());

        $avatar = (new Avatar($fetched1->getUserId(), 'new-filename.png'))
            ->setCreatedDate($fetched1->getCreatedDate())
            ->setLastModifiedDate($fetched1->getLastModifiedDate());

        sleep(1);

        $this->repository->update($avatar);

        $fetched2 = $this->repository->get($id);

        $this->assertEquals('new-filename.png', $fetched2->getFilename());
        $this->assertNotEquals($fetched1->getLastModifiedDate(), $fetched2->getLastModifiedDate());
    }

    public function test_exception_is_thrown_if_attempting_to_update_an_avatar_that_does_not_exist()
    {
        $id = Uuid::generate();
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Cannot update Avatar as Avatar with User ID {$id} does not exist");
        $this->repository->update(new Avatar($id, 'filename.jpg'));
    }
}
