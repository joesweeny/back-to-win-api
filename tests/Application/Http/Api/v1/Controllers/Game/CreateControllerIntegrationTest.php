<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Game;

use GamePlatform\Framework\DateTime\Clock;
use GamePlatform\Testing\Traits\CreateAuthToken;
use GamePlatform\Testing\Traits\RunsMigrations;
use GamePlatform\Testing\Traits\UsesContainer;
use GamePlatform\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class CreateControllerIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer,
        UsesHttpServer,
        CreateAuthToken;

    /** @var  ContainerInterface */
    private $container;
    /** @var  string */
    private $token;
    /** @var  Clock */
    private $clock;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->token = $this->getValidToken($this->container);
        $this->clock = $this->container->get(Clock::class);
    }

    public function test_returns_200_response_containing_game_data()
    {
        $data = (object) [
            'type' => 'GENERAL_KNOWLEDGE',
            'currency' => 'GBP',
            'buy_in' => 500,
            'max' => 50,
            'min' => 10,
            'start' => $this->clock->now()->addMinutes(200)->format(\DATE_ATOM),
            'players' => 4
        ];

        $request = new ServerRequest(
            'post',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($data)
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents())->data;

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(isset($json->game->id));
        $this->assertEquals('GENERAL_KNOWLEDGE', $json->game->type);
        $this->assertEquals('CREATED', $json->game->status);
        $this->assertEquals('GBP', $json->game->currency);
        $this->assertEquals(50, $json->game->max);
        $this->assertEquals(10, $json->game->min);
        $this->assertNotNull($json->game->start);
    }

    public function test_400_response_is_returned_if_game_type_property_is_not_a_valid_enum_property()
    {
        $data = (object) [
            'type' => 'SUPER_FUNKY_QUIZ',
            'currency' => 'GBP',
            'buy_in' => 500,
            'max' => 50,
            'min' => 10,
            'start' => '2018-12-03T12:00:00+01:00',
            'players' => 4
        ];

        $request = new ServerRequest(
            'post',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($data)
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            "Value 'SUPER_FUNKY_QUIZ' is not part of the enum GamePlatform\Domain\Game\Enum\GameType",
            $json->data->errors[0]->message
        );
    }

    public function test_400_response_is_returned_if_required_properties_are_missing_from_request_body()
    {
        $data = (object) [
            'currency' => 'GBP',
            'buy_in' => 500,
            'max' => 50,
            'min' => 10,
            'players' => 4
        ];

        $request = new ServerRequest(
            'post',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($data)
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Required field 'type' is missing", $json->data->errors[0]->message);
        $this->assertEquals("Required field 'start' is missing", $json->data->errors[1]->message);
    }

    public function test_422_response_is_returned_if_game_start_date_is_invalid()
    {
        $data = (object) [
            'type' => 'GENERAL_KNOWLEDGE',
            'currency' => 'GBP',
            'buy_in' => 500,
            'max' => 50,
            'min' => 10,
            'start' => $this->clock->now()->subMinutes(200)->format(\DATE_ATOM),
            'players' => 4
        ];

        $request = new ServerRequest(
            'post',
            '/api/game',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($data)
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(
            'Game start date must be later than the current date and time',
            $json->data->errors[0]->message
        );
    }
}
