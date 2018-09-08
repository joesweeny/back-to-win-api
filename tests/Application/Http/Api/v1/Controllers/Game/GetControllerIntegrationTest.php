<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Game;

use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Enum\GameType;
use GamePlatform\Domain\Game\GameOrchestrator;
use GamePlatform\Domain\GameEntry\GameEntryOrchestrator;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Domain\UserPurse\Entity\UserPurse;
use GamePlatform\Domain\UserPurse\UserPurseOrchestrator;
use GamePlatform\Framework\DateTime\Clock;
use GamePlatform\Framework\Password\PasswordHash;
use GamePlatform\Framework\Uuid\Uuid;
use GamePlatform\Testing\Traits\CreateAuthToken;
use GamePlatform\Testing\Traits\RunsMigrations;
use GamePlatform\Testing\Traits\UsesContainer;
use GamePlatform\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class GetControllerIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer,
        UsesHttpServer,
        CreateAuthToken;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Clock */
    private $clock;
    /** @var  string */
    private $token;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->clock = $this->container->get(Clock::class);
        $this->token = $this->getValidToken($this->container);
    }

    public function test_returns_200_response_containing_requested_game_and_game_entry_data()
    {
        $game = $this->createGame(
            4,
            $start = $this->clock->now()->addMinutes(200),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP'))
        );

        $user = $this->createUser('joe@joe.com', 'joe');

        $this->addUserToGame($game, $user);

        $request = new ServerRequest(
            'GET',
            "/api/game/{$game->getId()}",
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents())->data;

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('157e93d3-c225-4523-8a59-6630b05d671b', $json->game->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $json->game->type);
        $this->assertEquals('CREATED', $json->game->status);
        $this->assertEquals('GBP', $json->game->currency);
        $this->assertEquals(500, $json->game->buy_in);
        $this->assertEquals(50, $json->game->max);
        $this->assertEquals(10, $json->game->min);
        $this->assertEquals($start->format(\DATE_ATOM), $json->game->start);
        $this->assertTrue(isset($json->game->created_at));
        $this->assertTrue(isset($json->game->updated_at));
        $this->assertNotEmpty($json->users);
        $this->assertEquals('joe', $json->users[0]->username);
        $this->assertEquals('joe', $json->users[0]->username);
        $this->assertEquals('joe', $json->users[0]->username);
        $this->assertEquals('5a095ea0-bc3f-4534-a0ee-074e731a5892', $json->users[0]->id);
        $this->assertEquals('joe@joe.com', $json->users[0]->email);
        $this->assertTrue(isset($json->users[0]->created_at));
        $this->assertTrue(isset($json->users[0]->updated_at));
    }

    public function test_404_response_returned_if_game_does_not_exist()
    {
        $request = new ServerRequest(
            'GET',
            '/api/game/81644266-7b09-4a38-84db-f8c1584c2ad4',
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents())->data;

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(
            'Game with ID 81644266-7b09-4a38-84db-f8c1584c2ad4 does not exist',
            $json->errors[0]->message
        );
    }

    public function test_404_response_returned_if_id_provided_is_not_a_valid_uuid_string()
    {
        $request = new ServerRequest(
            'GET',
            '/api/game/999',
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents())->data;

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Game with ID 999 does not exist', $json->errors[0]->message);
    }

    private function createGame(int $players, \DateTimeImmutable $start, GameStatus $status, Money $buyIn): Game
    {
        return $this->container->get(GameOrchestrator::class)->createGame(
            new Game(
                new Uuid('157e93d3-c225-4523-8a59-6630b05d671b'),
                GameType::GENERAL_KNOWLEDGE(),
                $status,
                $buyIn,
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                $start,
                $players
            )
        );
    }

    private function createUser(string $email, string $username): User
    {
        $user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('5a095ea0-bc3f-4534-a0ee-074e731a5892'))
                ->setEmail($email)
                ->setUsername($username)
                ->setPasswordHash(new PasswordHash('password')),
            new Currency('GBP')
        );

        return $user;
    }

    private function addUserToGame(Game $game, User $user)
    {
        $this->container->get(GameEntryOrchestrator::class)->addGameEntry($game, $user);
    }
}
