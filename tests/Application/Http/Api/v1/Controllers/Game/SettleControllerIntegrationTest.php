<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Game;

use BackToWin\Bootstrap\Config;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\Game\GameOrchestrator;
use BackToWin\Domain\Game\Persistence\Writer;
use BackToWin\Domain\GameEntry\GameEntryOrchestrator;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\UserPurseOrchestrator;
use BackToWin\Framework\DateTime\Clock;
use BackToWin\Framework\Password\PasswordHash;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\CreateAuthToken;
use BackToWin\Testing\Traits\RunsMigrations;
use BackToWin\Testing\Traits\UsesContainer;
use BackToWin\Testing\Traits\UsesHttpServer;
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
            $this->clock->now()->sub(new \DateInterval('P1D')),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP'))
        );

        $user = $this->createUser('joe@joe.com', 'joe');

        $this->addUserToGame($game, $user);

        for ($i = 0; $i < 3; $i++) {
            $this->addUserToGame($game, $this->createUser("{$i}@joe.com", "user{$i}"));
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
            $this->clock->now()->sub(new \DateInterval('P1D')),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP'))
        );

        $user = $this->createUser('joe@joe.com', 'joe');

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
            $this->clock->now()->sub(new \DateInterval('P10D')),
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
        $user = $this->createUser('joe@joe.com', 'joe');

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

    public function test_404_response_is_returned_if_game_id_provided_is_not_a_valid_uuid()
    {
        $body = (object) [
            'game_id' => 1,
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
        $this->assertEquals('Invalid UUID string: 1', $json->data->errors[0]->message);
    }

    private function createGame(int $players, \DateTimeImmutable $start, GameStatus $status, Money $buyIn): Game
    {
        return $this->container->get(Writer::class)->insert(
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
            (new User())
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
