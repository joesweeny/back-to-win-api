<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Game;

use GamePlatform\Bootstrap\Config;
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

class SettleControllerIntegrationTest extends TestCase
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
        $this->container->get(Config::class)->set('log.logger', 'null');
        $this->token = $this->getValidToken($this->container);
    }

    public function test_200_response_is_returned_if_game_is_settled_successfully()
    {
        $game = $this->createGame(
            4,
            $this->clock->now()->add(new \DateInterval('P10D')),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP'))
        );

        $user = $this->createUser(
            $money = new Money(1000000, new Currency('GBP')),
            'joe@joe.com',
            'joe'
        );

        $this->addUserToGame($game, $user);

        for ($i = 0; $i < 3; $i++) {
            $this->addUserToGame($game, $this->createUser($money, "{$i}@joe.com", "user{$i}"));
        }

        $body = (object) [
            'game_id' => (string) $game->getId(),
            'user_id' => (string) $user->getId(),
            'currency' => 'GBP',
            'amount' => 1500
        ];

        $request = new ServerRequest(
            'POST',
            '/api/game/settle',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($body)
        );

        $response = $this->handle($this->container, $request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_422_response_returned_if_user_did_not_enter_game()
    {
        $game = $this->createGame(
            4,
            $this->clock->now()->add(new \DateInterval('P10D')),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP'))
        );

        $user = $this->createUser(
            $money = new Money(1000000, new Currency('GBP')),
            'joe@joe.com',
            'joe'
        );

        $body = (object) [
            'game_id' => (string) $game->getId(),
            'user_id' => (string) $user->getId(),
            'currency' => 'GBP',
            'amount' => 1500
        ];

        $request = new ServerRequest(
            'POST',
            '/api/game/settle',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($body)
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(
            "Unable to settle as User {$user->getId()} did not enter Game {$game->getId()}",
            $json->data->errors[0]->message
        );
    }

    public function test_404_response_if_user_does_not_exist()
    {
        $game = $this->createGame(
            4,
            $this->clock->now()->add(new \DateInterval('P10D')),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP'))
        );

        $body = (object) [
            'game_id' => (string) $game->getId(),
            'user_id' => (string) $id = Uuid::generate(),
            'currency' => 'GBP',
            'amount' => 1500
        ];

        $request = new ServerRequest(
            'POST',
            '/api/game/settle',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($body)
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("User with ID '{$id}' does not exist", $json->data->errors[0]->message);
    }

    public function test_404_response_returned_if_game_does_not_exist()
    {
        $user = $this->createUser(
            $money = new Money(1000000, new Currency('GBP')),
            'joe@joe.com',
            'joe'
        );

        $body = (object) [
            'game_id' => (string) $id = Uuid::generate(),
            'user_id' => (string) $user->getId(),
            'currency' => 'GBP',
            'amount' => 1500
        ];

        $request = new ServerRequest(
            'POST',
            '/api/game/settle',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($body)
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(
            "Unable to retrieve Game {$id} as it does not exist",
            $json->data->errors[0]->message
        );
    }

    public function test_400_response_returned_if_request_body_is_missing_required_fields()
    {
        $body = (object) [
            'game_id' => (string) Uuid::generate(),
            'user_id' => (string) Uuid::generate(),
        ];

        $request = new ServerRequest(
            'POST',
            '/api/game/settle',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($body)
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Required field 'currency' is missing", $json->data->errors[0]->message);
        $this->assertEquals("Required field 'amount' is missing", $json->data->errors[1]->message);
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

    private function createUser(Money $balance, string $email, string $username): User
    {
        $user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User())
                ->setEmail($email)
                ->setUsername($username)
                ->setPasswordHash(new PasswordHash('password'))
        );

        $this->container->get(UserPurseOrchestrator::class)->updateUserPurse(
            (new UserPurse($user->getId(), $balance))->setCreatedDate($this->clock->now())
        );

        return $user;
    }

    private function addUserToGame(Game $game, User $user)
    {
        $this->container->get(GameEntryOrchestrator::class)->addGameEntry($game, $user);
    }
}
