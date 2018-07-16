<?php

namespace BackToWin\Domain\User\Persistence\Illuminate;

use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\Persistence\Reader;
use BackToWin\Domain\User\Persistence\Writer;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Password\PasswordHash;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\RunsMigrations;
use BackToWin\Testing\Traits\UsesContainer;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class IlluminateReaderIntegrationTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

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

    public function test_interface_implementation_is_bound()
    {
        $this->assertInstanceOf(Reader::class, $this->reader);
    }

    public function test_user_can_be_retrieved_by_email()
    {
        $this->writer->insert(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $fetched = $this->reader->getByEmail('joe@example.com');

        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $fetched->getId()->__toString());
        $this->assertEquals('joe@example.com', $fetched->getEmail());
    }

    public function test_exception_is_thrown_if_email_is_not_present_in_database()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("User with email 'fake@email.com' does not exist");
        $this->reader->getByEmail('fake@email.com');
    }

    public function test_a_user_can_be_retrieved_by_their_id()
    {
        $this->writer->insert(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $fetched = $this->reader->getById(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));

        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $fetched->getId()->__toString());
        $this->assertEquals('joe@example.com', $fetched->getEmail());
    }

    public function test_exception_is_thrown_if_id_is_not_present_in_the_database()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("User with ID 'dc5b6421-d452-4862-b741-d43383c3fe1d' does not exist");
        $this->reader->getById(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));
    }

    public function test_a_user_can_be_retrieved_by_their_username()
    {
        $this->writer->insert(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $fetched = $this->reader->getByUsername('joesweeny');

        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $fetched->getId()->__toString());
        $this->assertEquals('joe@example.com', $fetched->getEmail());
    }

    public function test_exception_is_thrown_if_username_is_not_present_in_the_database()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("User with username 'joesweeny' does not exist");
        $this->reader->getByUsername('joesweeny');
    }

    public function test_gets_users_returns_an_array_of_users_sorted_alphabetically_by_email()
    {
        $this->writer->insert(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->writer->insert(
            (new User('fbeb2f20-b1a4-433f-8f83-bb6f83c01cfa'))
                ->setUsername('andreasweeny')
                ->setEmail('andrea@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->writer->insert(
            (new User('77e2438d-a744-4590-9785-08917dcdeb75'))
                ->setUsername('thomasweeny')
                ->setEmail('thomas@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $users = $this->reader->getUsers();

        $this->assertCount(3, $users);
        $this->assertEquals('andrea@example.com', $users[0]->getEmail());
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }
}
