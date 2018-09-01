<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Game;

use GamePlatform\Bootstrap\Config;
use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Enum\GameType;
use GamePlatform\Domain\Game\GameOrchestrator;
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

class EnterControllerIntegrationTest extends TestCase
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

    public function test_user_can_successfully_be_entered_into_a_game()
    {
        $game = $this->createGame(
            4,
            $this->clock->now()->add(new \DateInterval('P10D')),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP'))
        );

        $user = $this->createUser(new Money(1000000, new Currency('GBP')));

        $request = new ServerRequest(
            'POST',
            "/api/game/{$game->getId()}/user/{$user->getId()}",
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_422_response_returned_if_user_cannot_enter_game_due_to_insufficient_funds()
    {
        $game = $this->createGame(
            4,
            $this->clock->now()->add(new \DateInterval('P10D')),
            GameStatus::CREATED(),
            new Money(50000, new Currency('GBP'))
        );

        $user = $this->createUser(new Money(1000000, new Currency('GBP')));

        $request = new ServerRequest(
            'POST',
            "/api/game/{$game->getId()}/user/{$user->getId()}",
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(
            'User 36e26d37-703c-4beb-999d-6d97b5dea9e3 cannot enter Game 157e93d3-c225-4523-8a59-6630b05d671b. ' .
            'Message: Cannot withdraw money for User 36e26d37-703c-4beb-999d-6d97b5dea9e3 due to insufficient funds',
            $json->data->errors[0]->message
        );
    }

    public function test_404_response_is_returned_if_game_does_not_exist()
    {
        $gameId = Uuid::generate();

        $user = $this->createUser(new Money(1000000, new Currency('GBP')));

        $request = new ServerRequest(
            'POST',
            "/api/game/{$gameId}/user/{$user->getId()}",
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("Unable to retrieve Game {$gameId} as it does not exist", $json->data->errors[0]->message);
    }

    public function test_404_response_is_returned_if_user_does_not_exist()
    {
        $game = $this->createGame(
            4,
            $this->clock->now()->add(new \DateInterval('P10D')),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP'))
        );

        $userId = Uuid::generate();

        $request = new ServerRequest(
            'POST',
            "/api/game/{$game->getId()}/user/{$userId}",
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("User with ID '{$userId}' does not exist", $json->data->errors[0]->message);
    }

    public function test_422_response_is_returned_if_game_is_not_in_the_correct_state()
    {
        $game = $this->createGame(
            4,
            $this->clock->now()->add(new \DateInterval('P10D')),
            GameStatus::COMPLETED(),
            new Money(50000, new Currency('GBP'))
        );

        $user = $this->createUser(new Money(1000000, new Currency('GBP')));

        $request = new ServerRequest(
            'POST',
            "/api/game/{$game->getId()}/user/{$user->getId()}",
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(
            'Cannot enter Game 157e93d3-c225-4523-8a59-6630b05d671b as game status is COMPLETED',
            $json->data->errors[0]->message
        );
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

    private function createUser(Money $balance): User
    {
        $user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('36e26d37-703c-4beb-999d-6d97b5dea9e3'))
                ->setEmail('joe@joe.com')
                ->setUsername('Joe')
                ->setPasswordHash(new PasswordHash('password'))
        );

        $this->container->get(UserPurseOrchestrator::class)->updateUserPurse(
            (new UserPurse($user->getId(), $balance))->setCreatedDate($this->clock->now())
        );

        return $user;
    }
}
