<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\User;

use GamePlatform\Testing\Traits\RunsMigrations;
use GamePlatform\Testing\Traits\UsesContainer;
use GamePlatform\Testing\Traits\UsesHttpServer;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Framework\Password\PasswordHash;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class CreateControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
    }

    public function test_success_response_is_received_containing_user_data_if_created_successfully()
    {
        $request = new ServerRequest(
            'post',
            '/api/user',
            [],
            '{"username":"joesweeny","email":"joe@email.com","password":"mypass"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('joe@email.com', $jsend->data->user->email);
        $this->assertEquals('joesweeny', $jsend->data->user->username);
        $this->assertTrue(isset($jsend->data->user->id));
    }

    public function test_422_response_is_returned_if_user_creation_process_fails()
    {
        $this->container->get(UserOrchestrator::class)->createUser(
            (new User)
                ->setUsername('joesweeny')
                ->setEmail('joe@email.com')
                ->setPasswordHash(PasswordHash::createFromRaw('pass')));

        $request = new ServerRequest(
            'post',
            '/api/user',
            [],
            '{"username":"joesweeny","email":"joe@email.com","password":"mypass"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(
            'A user has already registered with this email address joe@email.com',
            $jsend->data->errors[0]->message
        );
    }

    public function test_400_response_is_returned_if_request_body_is_missing()
    {
        $request = new ServerRequest('post', '/api/user', []);

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Unable to parse request body', $jsend->data->errors[0]->message);
    }

    public function test_400_response_is_returned_if_required_request_body_parameters_are_missing()
    {
        $request = new ServerRequest(
            'post',
            '/api/user',
            [],
            '{"username":"joesweeny"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Required field 'email' is missing", $jsend->data->errors[0]->message);
        $this->assertEquals("Required field 'password' is missing", $jsend->data->errors[1]->message);
    }
}
