<?php

namespace GamePlatform\Domain\User\Persistence\Illuminate;

use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\User\Persistence\Reader;
use GamePlatform\Domain\User\Persistence\Writer;
use GamePlatform\Framework\Password\PasswordHash;
use GamePlatform\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use GamePlatform\Testing\Traits\RunsMigrations;
use GamePlatform\Testing\Traits\UsesContainer;

class IlluminateWriterIntegrationTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Writer */
    private $writer;
    /** @var  Connection */
    private $connection;
    /** @var  Reader */
    private $reader;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->writer = $this->container->get(Writer::class);
        $this->reader = $this->container->get(Reader::class);
        $this->connection = $this->container->get(Connection::class);
    }

    public function test_interface_implementation_is_bound()
    {
        $this->assertInstanceOf(Writer::class, $this->writer);
    }

    public function test_create_user_adds_a_new_record_to_the_database()
    {
        $this->writer->insert(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->assertCount(1, $this->connection->table('user')->get());

        $this->writer->insert(
            (new User('a4a93668-6e61-4a81-93b4-b2404dbe9788'))
                ->setUsername('andreasweeny')
                ->setEmail('andrea@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->assertCount(2, $this->connection->table('user')->get());
    }

    public function test_a_user_can_be_deleted_from_the_database()
    {
        $user = $this->writer->insert(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->assertCount(1, $this->connection->table('user')->get());

        $this->writer->delete($user);

        $this->assertCount(0, $this->connection->table('user')->get());
    }

    public function test_update_user_updates_a_user_record_in_the_database()
    {
        $this->writer->insert(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $fetched = $this->reader->getById(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));

        $this->assertInstanceOf(User::class, $fetched);
        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $fetched->getId()->__toString());

        $this->writer->update($fetched->setEmail('joe@email.com'));

        $fetched = $this->reader->getById(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));

        $this->assertEquals('joe@email.com', $fetched->getEmail());
    }
}
