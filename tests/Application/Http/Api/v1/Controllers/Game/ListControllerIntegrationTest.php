<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Game;

use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Enum\GameType;
use GamePlatform\Domain\Game\GameOrchestrator;
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

class ListControllerIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer,
        UsesHttpServer,
        CreateAuthToken;

    /** @var  ContainerInterface */
    private $container;
    /** @var  GameOrchestrator */
    private $orchestrator;
    /** @var  string */
    private $token;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(GameOrchestrator::class);
        $this->token = $this->getValidToken($this->container);
    }
    
    public function test_200_response_is_returned_containing_an_array_of_game_data()
    {
        $this->createGames();

        $request = new ServerRequest(
            'GET',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"]
        );
        
        $response = $this->handle($this->container, $request);
        
        $json = json_decode($response->getBody()->getContents());
        
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2', $json->data->games[0]->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $json->data->games[0]->type);
        $this->assertEquals('CREATED', $json->data->games[0]->status);
        $this->assertEquals('EUR', $json->data->games[0]->currency);
        $this->assertEquals(500, $json->data->games[0]->buy_in);
        $this->assertEquals(50, $json->data->games[0]->max);
        $this->assertEquals(10, $json->data->games[0]->min);
        $this->assertEquals(4, $json->data->games[0]->players);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $json->data->games[0]->start);

        $this->assertEquals('ad3f0975-25d1-4078-92d9-c964a3a131ba', $json->data->games[1]->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $json->data->games[1]->type);
        $this->assertEquals('COMPLETED', $json->data->games[1]->status);
        $this->assertEquals('GBP', $json->data->games[1]->currency);
        $this->assertEquals(15000, $json->data->games[1]->buy_in);
        $this->assertEquals(5000, $json->data->games[1]->max);
        $this->assertEquals(100, $json->data->games[1]->min);
        $this->assertEquals(4, $json->data->games[1]->players);
        $this->assertEquals('2018-07-25T00:00:00+00:00', $json->data->games[1]->start);
    }

    public function test_games_can_be_filtered_by_status()
    {
        $this->createGames();

        $request = (new ServerRequest(
            'GET',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"]
        ))->withQueryParams(['status' => 'CREATED']);

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $json->data->games);
        $this->assertEquals('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2', $json->data->games[0]->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $json->data->games[0]->type);
        $this->assertEquals('CREATED', $json->data->games[0]->status);
        $this->assertEquals('EUR', $json->data->games[0]->currency);
        $this->assertEquals(500, $json->data->games[0]->buy_in);
        $this->assertEquals(50, $json->data->games[0]->max);
        $this->assertEquals(10, $json->data->games[0]->min);
        $this->assertEquals(4, $json->data->games[0]->players);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $json->data->games[0]->start);
    }

    public function test_games_can_be_filtered_by_start_date_time()
    {
        $this->createGames();

        $request = (new ServerRequest(
            'GET',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"]
        ))->withQueryParams(['start' => '2018-07-19T00:00:00+00:00']);

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $json->data->games);
        $this->assertEquals('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2', $json->data->games[0]->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $json->data->games[0]->type);
        $this->assertEquals('CREATED', $json->data->games[0]->status);
        $this->assertEquals('EUR', $json->data->games[0]->currency);
        $this->assertEquals(500, $json->data->games[0]->buy_in);
        $this->assertEquals(50, $json->data->games[0]->max);
        $this->assertEquals(10, $json->data->games[0]->min);
        $this->assertEquals(4, $json->data->games[0]->players);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $json->data->games[0]->start);
    }

    public function test_games_can_be_filtered_by_buy_in()
    {
        $this->createGames();

        $request = (new ServerRequest(
            'GET',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"]
        ))->withQueryParams(['buy_in' => 1000]);

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $json->data->games);
        $this->assertEquals('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2', $json->data->games[0]->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $json->data->games[0]->type);
        $this->assertEquals('CREATED', $json->data->games[0]->status);
        $this->assertEquals('EUR', $json->data->games[0]->currency);
        $this->assertEquals(500, $json->data->games[0]->buy_in);
        $this->assertEquals(50, $json->data->games[0]->max);
        $this->assertEquals(10, $json->data->games[0]->min);
        $this->assertEquals(4, $json->data->games[0]->players);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $json->data->games[0]->start);
    }

    public function test_games_can_be_filtered_by_currency()
    {
        $this->createGames();

        $request = (new ServerRequest(
            'GET',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"]
        ))->withQueryParams(['currency' => 'GBP']);

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $json->data->games);
        $this->assertEquals('ad3f0975-25d1-4078-92d9-c964a3a131ba', $json->data->games[0]->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $json->data->games[0]->type);
        $this->assertEquals('COMPLETED', $json->data->games[0]->status);
        $this->assertEquals('GBP', $json->data->games[0]->currency);
        $this->assertEquals(15000, $json->data->games[0]->buy_in);
        $this->assertEquals(5000, $json->data->games[0]->max);
        $this->assertEquals(100, $json->data->games[0]->min);
        $this->assertEquals(4, $json->data->games[0]->players);
        $this->assertEquals('2018-07-25T00:00:00+00:00', $json->data->games[0]->start);
    }

    public function test_400_response_returned_if_providing_invalid_arguments_for_query_strings()
    {
        $request = (new ServerRequest(
            'GET',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"]
        ))->withQueryParams(['status' => 'DONE']);

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            "Value 'DONE' is not part of the enum GamePlatform\Domain\Game\Enum\GameStatus",
            $json->data->errors[0]->message
        );

        $request = (new ServerRequest(
            'GET',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"]
        ))->withQueryParams(['buy_in' => '100 Quid']);

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Parameter 'buy_in' needs to be numeric", $json->data->errors[0]->message);

        $request = (new ServerRequest(
            'GET',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"]
        ))->withQueryParams(['start' => 'Hello']);

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Date provided in not in a valid format', $json->data->errors[0]->message);
    }
    
    private function createGames(): void
    {
        $this->orchestrator->createGame(
            new Game(
                new Uuid('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2'),
                GameType::GENERAL_KNOWLEDGE(),
                GameStatus::CREATED(),
                new Money(500, new Currency('EUR')),
                new Money(50, new Currency('EUR')),
                new Money(10, new Currency('EUR')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );

        $this->orchestrator->createGame(
            new Game(
                new Uuid('ad3f0975-25d1-4078-92d9-c964a3a131ba'),
                GameType::GENERAL_KNOWLEDGE(),
                GameStatus::COMPLETED(),
                new Money(15000, new Currency('GBP')),
                new Money(5000, new Currency('GBP')),
                new Money(100, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-25 00:00:00'),
                4
            )
        );
    }
}
