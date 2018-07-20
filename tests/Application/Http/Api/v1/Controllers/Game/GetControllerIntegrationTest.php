<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Game;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\Game\Orchestrator;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\RunsMigrations;
use BackToWin\Testing\Traits\UsesContainer;
use BackToWin\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class GetControllerIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer,
        UsesHttpServer;

    /** @var  ContainerInterface */
    private $container;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->container->get(Orchestrator::class)->createGame(
            new Game(
                new Uuid('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2'),
                GameType::GENERAL_KNOWLEDGE(),
                GameStatus::CREATED(),
                new Money(50, new Currency('GBP')),
                new Money(10, new Currency('GBP')),
                new \DateTimeImmutable('2018-07-18 00:00:00'),
                4
            )
        );
    }

    public function test_returns_200_response_containing_requested_game_data()
    {
        $request = new ServerRequest('GET', '/api/game/a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2');

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents())->data;

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2', $json->game->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $json->game->type);
        $this->assertEquals('CREATED', $json->game->status);
        $this->assertEquals('GBP', $json->game->currency);
        $this->assertEquals(50, $json->game->max);
        $this->assertEquals(10, $json->game->min);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $json->game->start);
        $this->assertTrue(isset($json->game->created_at));
        $this->assertTrue(isset($json->game->updated_at));
    }

    public function test_404_response_returned_if_game_does_not_exist()
    {
        $request = new ServerRequest('GET', '/api/game/81644266-7b09-4a38-84db-f8c1584c2ad4');

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
        $request = new ServerRequest('GET', '/api/game/999');

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents())->data;

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Game with ID 999 does not exist', $json->errors[0]->message);
    }
}
