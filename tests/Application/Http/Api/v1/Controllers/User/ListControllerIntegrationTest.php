<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\User;

use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Framework\Password\PasswordHash;
use GamePlatform\Testing\Traits\CreateAuthToken;
use GamePlatform\Testing\Traits\RunsMigrations;
use GamePlatform\Testing\Traits\UsesContainer;
use GamePlatform\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use Money\Currency;
use PHPUnit\Framework\TestCase;

class ListControllerIntegrationTest extends TestCase
{
    use UsesHttpServer,
        UsesContainer,
        RunsMigrations,
        CreateAuthToken;

    /** @var  ContainerInterface */
    private $container;
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  string */
    private $token;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(UserOrchestrator::class);
        $this->orchestrator->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))
                ->setUsername('joesweeny')
                ->setEmail('joe@joe.com')
                ->setPasswordHash(new PasswordHash('password')),
            new Currency('GBP')
        );
        sleep(1);
        $this->orchestrator->createUser(
            (new User('0b854053-0cef-4160-973e-6a5390ec0617'))
                ->setUsername('andreasweeny')
                ->setEmail('andrea@andrea.com')
                ->setPasswordHash(new PasswordHash('password')),
            new Currency('GBP')
        );
        $this->token = $this->getValidToken($this->container);
    }

    public function test_returns_200_response_with_body_containing_a_list_of_user_details()
    {
        $request = new ServerRequest(
            'GET',
            '/api/user',
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('f530caab-1767-4f0c-a669-331a7bf0fc85', $json->data->users[0]->id);
        $this->assertEquals('joesweeny', $json->data->users[0]->username);
        $this->assertEquals('joe@joe.com', $json->data->users[0]->email);
        $this->assertEquals('0b854053-0cef-4160-973e-6a5390ec0617', $json->data->users[1]->id);
        $this->assertEquals('andreasweeny', $json->data->users[1]->username);
        $this->assertEquals('andrea@andrea.com', $json->data->users[1]->email);
    }
}
