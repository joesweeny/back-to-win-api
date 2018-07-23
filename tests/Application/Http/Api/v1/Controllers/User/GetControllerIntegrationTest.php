<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\User;

use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Framework\Password\PasswordHash;
use BackToWin\Testing\Traits\RunsMigrations;
use BackToWin\Testing\Traits\UsesContainer;
use BackToWin\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class GetControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  User */
    private $user;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(UserOrchestrator::class);
        $this->user = $this->orchestrator->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))
                ->setUsername('joesweeny')
                ->setEmail('joe@joe.com')
                ->setPasswordHash(new PasswordHash('password'))
        );
    }

    public function test_success_response_is_received_with_user_details()
    {
        $request = new ServerRequest(
            'get',
            '/api/user/f530caab-1767-4f0c-a669-331a7bf0fc85',
            []
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('f530caab-1767-4f0c-a669-331a7bf0fc85', $jsend->data->user->id);
        $this->assertEquals('joe@joe.com', $jsend->data->user->email);
    }

    public function test_404_response_returned_if_user_details_cannot_be_retrieved()
    {
        $request = new ServerRequest(
            'get',
            '/api/user/93449e9d-4082-4305-8840-fa1673bcf915',
            []
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(
            'User with ID 93449e9d-4082-4305-8840-fa1673bcf915 does not exist',
            $jsend->data->errors[0]->message
        );
    }

    public function test_404_response_is_returned_if_id_provided_is_not_a_valid_uuid_string()
    {
        $request = new ServerRequest(
            'get',
            '/api/user/1',
            []
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('User with ID 1 does not exist', $jsend->data->errors[0]->message);
    }
}
