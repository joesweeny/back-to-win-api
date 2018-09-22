<?php

namespace BackToWin\Domain\GameEntry\Persistence\Illuminate;

use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Persistence\Repository;
use BackToWin\Framework\DateTime\FixedClock;
use BackToWin\Framework\Exception\RepositoryDuplicationException;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\RunsMigrations;
use BackToWin\Testing\Traits\UsesContainer;
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

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->repository = new IlluminateRepository(
            $this->container->get(Connection::class),
            new FixedClock(new \DateTimeImmutable('2018-07-23 21:53:00'))
        );
    }

    public function test_insert_adds_new_record_to_the_database()
    {
        $gameId = new Uuid('70801f67-75a4-4c09-bb22-d6287f7d15e5');
        $userId = new Uuid('6ef8adcc-2b22-46ea-970f-a5d41ed110b3');

        $entry = $this->repository->insert($gameId, $userId);

        $this->assertEquals('70801f67-75a4-4c09-bb22-d6287f7d15e5', $entry->getGameId());
        $this->assertEquals('6ef8adcc-2b22-46ea-970f-a5d41ed110b3', $entry->getUserId());
    }

    public function test_exception_is_thrown_if_attempting_to_insert_a_record_that_exists()
    {
        $gameId = new Uuid('70801f67-75a4-4c09-bb22-d6287f7d15e5');
        $userId = new Uuid('6ef8adcc-2b22-46ea-970f-a5d41ed110b3');

        $this->repository->insert($gameId, $userId);

        $this->expectException(RepositoryDuplicationException::class);
        $this->expectExceptionMessage(
            'User 6ef8adcc-2b22-46ea-970f-a5d41ed110b3 has already entered game 70801f67-75a4-4c09-bb22-d6287f7d15e5'
        );
        $this->repository->insert($gameId, $userId);
    }

    public function test_get_returns_an_array_of_game_entry_objects_for_a_specific_game()
    {
        $gameId = new Uuid('70801f67-75a4-4c09-bb22-d6287f7d15e5');
        $user1 = new Uuid('6ef8adcc-2b22-46ea-970f-a5d41ed110b3');
        $user2 = new Uuid('4802f8bc-7f4d-441d-a433-98e568cfbfd9');
        $user3 = new Uuid('c2011328-303f-4764-a01e-8c686b5756a3');

        $this->repository->insert($gameId, $user1);
        $this->repository->insert($gameId, $user2);
        $this->repository->insert($gameId, $user3);

        $entries = $this->repository->get($gameId);
        
        foreach ($entries as $entry) {
            $this->assertInstanceOf(GameEntry::class, $entry);
            $this->assertEquals('70801f67-75a4-4c09-bb22-d6287f7d15e5', $entry->getGameId());
        }

        $this->assertEquals('6ef8adcc-2b22-46ea-970f-a5d41ed110b3', (string) $entries[1]->getUserId());
        $this->assertEquals('4802f8bc-7f4d-441d-a433-98e568cfbfd9', (string) $entries[0]->getUserId());
        $this->assertEquals('c2011328-303f-4764-a01e-8c686b5756a3', (string) $entries[2]->getUserId());
    }

    public function test_exists_returns_true_if_user_has_entered_game()
    {
        $gameId = new Uuid('70801f67-75a4-4c09-bb22-d6287f7d15e5');
        $userId = new Uuid('6ef8adcc-2b22-46ea-970f-a5d41ed110b3');

        $this->repository->insert($gameId, $userId);

        $this->assertTrue($this->repository->exists($gameId, $userId));
    }

    public function test_exists_returns_false_if_user_has_not_entered_a_game()
    {
        $this->assertFalse($this->repository->exists(Uuid::generate(), Uuid::generate()));
    }
}
