<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Auth;

use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Framework\Password\PasswordHash;
use GamePlatform\Testing\Traits\RunsMigrations;
use GamePlatform\Testing\Traits\UsesContainer;
use GamePlatform\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use Money\Currency;
use PHPUnit\Framework\TestCase;

class TokenControllerIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer,
        UsesHttpServer;

    /** @var  ContainerInterface */
    private $container;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
    }

    public function test_200_response_returned_with_token_in_response_body()
    {
        $this->createUser();

        $request = new ServerRequest(
            'POST',
            '/auth/token',
            [],
            '{"email": "joe@joe.com", "password": "password"}'
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $json->status);
        $this->assertNotNull($json->data->token);
    }

    public function test_403_response_returned_if_user_credentials_are_incorrect()
    {
        $this->createUser();

        $request = new ServerRequest(
            'POST',
            '/auth/token',
            [],
            '{"email": "joe@joe.com", "password": "wrong-password"}'
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('Unable to generate token with credentials provided', $json->data->errors[0]->message);
    }

    public function test_403_response_is_returned_if_a_user_does_not_exist()
    {
        $request = new ServerRequest(
            'POST',
            '/auth/token',
            [],
            '{"email": "joe@joe.com", "password": "wrong-password"}'
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('Unable to generate token with credentials provided', $json->data->errors[0]->message);
    }

    public function test_400_response_returned_if_required_email_parameter_is_missing()
    {
        $request = new ServerRequest(
            'POST',
            '/auth/token',
            [],
            '{"password": "wrong-password"}'
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Request body is missing 'email' parameter", $json->data->errors[0]->message);
    }

    public function test_400_response_returned_if_required_password_parameter_is_missing()
    {
        $request = new ServerRequest(
            'POST',
            '/auth/token',
            [],
            '{"email": "joe@joe.com"}'
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Request body is missing 'password' parameter", $json->data->errors[0]->message);
    }

    public function test_400_response_returned_if_request_body_is_missing()
    {
        $request = new ServerRequest(
            'POST',
            '/auth/token',
            []
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Unable to parse request body', $json->data->errors[0]->message);
    }

    private function createUser()
    {
        $this->container->get(UserOrchestrator::class)->createUser(
            (new User())
                ->setEmail('joe@joe.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
                ->setUsername('joe'),
            new Currency('GBP')
        );
    }
}
