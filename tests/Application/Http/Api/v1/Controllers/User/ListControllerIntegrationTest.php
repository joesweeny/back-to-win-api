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

class ListControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  UserOrchestrator */
    private $orchestrator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(UserOrchestrator::class);
        $this->orchestrator->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))
                ->setUsername('joesweeny')
                ->setEmail('joe@joe.com')
                ->setPasswordHash(new PasswordHash('password'))
        );
        sleep(1);
        $this->orchestrator->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))
                ->setUsername('andreasweeny')
                ->setEmail('andrea@andrea.com')
                ->setPasswordHash(new PasswordHash('password'))
        );
    }

    public function test_returns_200_response_with_body_containing_a_list_of_user_details()
    {
        $request = new ServerRequest('GET', '/api/user');

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('f530caab-1767-4f0c-a669-331a7bf0fc85', $json->data->users[0]->id);
        $this->assertEquals('joesweeny', $json->data->users[0]->username);
        $this->assertEquals('joe@joe.com', $json->data->users[0]->email);
        $this->assertEquals('f530caab-1767-4f0c-a669-331a7bf0fc85', $json->data->users[1]->id);
        $this->assertEquals('andreasweeny', $json->data->users[1]->username);
        $this->assertEquals('andrea@andrea.com', $json->data->users[1]->email);
    }
}
