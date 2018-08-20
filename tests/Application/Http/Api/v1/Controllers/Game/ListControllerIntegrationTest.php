<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Game;

use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Enum\GameType;
use GamePlatform\Domain\Game\GameOrchestrator;
use GamePlatform\Framework\Uuid\Uuid;
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
        UsesHttpServer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  GameOrchestrator */
    private $orchestrator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(GameOrchestrator::class);
    }
    
    public function test_200_response_is_returned_containing_an_array_of_game_data()
    {
        $this->createGames();

        $request = new ServerRequest('GET', '/api/game');
        
        $response = $this->handle($this->container, $request);
        
        $json = json_decode($response->getBody()->getContents());
        
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2', $json->data->games[0]->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $json->data->games[0]->type);
        $this->assertEquals('CREATED', $json->data->games[0]->status);
        $this->assertEquals('GBP', $json->data->games[0]->currency);
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
        $this->assertEquals('2018-07-18T00:00:00+00:00', $json->data->games[1]->start);
    }
    
    private function createGames(): void
    {
        $this->orchestrator->createGame(
            new Game(
                new Uuid('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2'),
                GameType::GENERAL_KNOWLEDGE(),
                GameStatus::CREATED(),
                new Money(500, new Currency('GBP')),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
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
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );
    }
}
