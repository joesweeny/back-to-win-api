<?php

namespace GamePlatform\Domain\User;

use GamePlatform\Domain\UserPurse\UserPurseOrchestrator;
use GamePlatform\Framework\Exception\NotAuthenticatedException;
use GamePlatform\Testing\Traits\RunsMigrations;
use GamePlatform\Testing\Traits\UsesContainer;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Password\PasswordHash;
use GamePlatform\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class UserOrchestratorTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  Connection */
    private $connection;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(UserOrchestrator::class);
        $this->connection = $this->container->get(Connection::class);
    }

    public function test_create_user_adds_a_new_record_to_the_database()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->assertCount(1, $this->connection->table('user')->get());

        $this->orchestrator->createUser(
            (new User('a4a93668-6e61-4a81-93b4-b2404dbe9788'))
                ->setUsername('andreasweeny')
                ->setEmail('andrea@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->assertCount(2, $this->connection->table('user')->get());
    }

    public function test_user_can_be_retrieved_by_email()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $fetched = $this->orchestrator->getUserByEmail('joe@example.com');

        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $fetched->getId()->__toString());
        $this->assertEquals('joe@example.com', $fetched->getEmail());
    }

    public function test_exception_is_thrown_if_email_is_not_present_in_database()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("User with email 'fake@email.com' does not exist");
        $this->orchestrator->getUserByEmail('fake@email.com');
    }

    public function test_a_user_can_be_deleted_from_the_database()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $user =$this->orchestrator->getUserById(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));

        $this->assertCount(1, $this->connection->table('user')->get());

        $this->orchestrator->deleteUser($user);

        $this->assertCount(0, $this->connection->table('user')->get());
    }

    public function test_a_user_can_be_retrieved_by_their_id()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $fetched = $this->orchestrator->getUserById(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));

        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $fetched->getId()->__toString());
        $this->assertEquals('joe@example.com', $fetched->getEmail());
    }

    public function test_exception_is_thrown_if_id_is_not_present_in_the_database()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("User with ID 'dc5b6421-d452-4862-b741-d43383c3fe1d' does not exist");
        $this->orchestrator->getUserById(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));
    }

    public function test_verify_user_returns_user_if_password_matches_password_stored_for_user()
    {
        $this->orchestrator->createUser(
            $user = (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $verified = $this->orchestrator->verifyUser('joe@example.com', 'password');

        $this->assertEquals($user->getId(), $verified->getId());
    }

    public function test_verify_user_throws_exception_if_password_does_not_match_password_stored_for_user()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->expectException(NotAuthenticatedException::class);
        $this->orchestrator->verifyUser('joe@example.com', 'wrongPassword');
    }

    public function test_gets_users_returns_a_collection_of_users_sorted_alphabetically_by_email()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        sleep(1);

        $this->orchestrator->createUser(
            (new User('fbeb2f20-b1a4-433f-8f83-bb6f83c01cfa'))
                ->setUsername('andreasweeny')
                ->setEmail('andrea@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        sleep(1);

        $this->orchestrator->createUser(
            (new User('77e2438d-a744-4590-9785-08917dcdeb75'))
                ->setUsername('thomasweeny')
                ->setEmail('thomas@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $users = $this->orchestrator->getUsers();

        $this->assertCount(3, $users);
        $this->assertEquals('joe@example.com', $users[0]->getEmail());
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    public function test_purse_is_created_for_user_when_a_user_is_created()
    {
        $purseOrchestrator = $this->container->get(UserPurseOrchestrator::class);

        $this->orchestrator->createUser(
            $user = (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $purse = $purseOrchestrator->getUserPurse($user->getId());

        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $purse->getUserId());
        $this->assertEquals(new Money(0, new Currency('GBP')), $purse->getTotal());
    }
}
