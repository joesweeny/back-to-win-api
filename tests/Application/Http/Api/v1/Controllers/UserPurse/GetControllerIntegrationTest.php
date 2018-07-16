<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\UserPurse;

use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Persistence\Writer;
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
    use UsesContainer,
        UsesHttpServer,
        RunsMigrations;

    /** @var  ContainerInterface */
    private $container;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->container->get(Writer::class)->insert(
            (new UserPurse())
                ->setUserId(new Uuid('511f27c9-58be-49a5-82f1-a8b8807c2075'))
                ->setTotal(new Money(500, new Currency('GBP')))
        );
    }

    public function test_200_response_is_returned_containing_user_purse_data()
    {
        $request = new ServerRequest('get', '/api/user/511f27c9-58be-49a5-82f1-a8b8807c2075/purse');

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('511f27c9-58be-49a5-82f1-a8b8807c2075', $json->data->purse->user_id);
        $this->assertEquals(500, $json->data->purse->amount);
        $this->assertEquals('GBP', $json->data->purse->currency);
    }

    public function test_404_response_is_returned_if_user_purse_does_not_exist()
    {
        $request = new ServerRequest('get', '/api/user/d8a3a1e7-d169-44bb-a848-aad07ccffcba/purse');

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(
            'Purse for User d8a3a1e7-d169-44bb-a848-aad07ccffcba does not exist',
            $json->data->errors[0]->message
        );
    }

    public function test_404_response_is_returned_if_user_id_is_not_a_valid_uuid()
    {
        $request = new ServerRequest('get', '/api/user/1/purse');

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(
            'Purse for User 1 does not exist',
            $json->data->errors[0]->message
        );
    }
}
