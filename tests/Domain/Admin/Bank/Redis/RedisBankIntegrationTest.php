<?php

namespace GamePlatform\Domain\Admin\Bank\Redis;

use GamePlatform\Bootstrap\Config;
use GamePlatform\Domain\Admin\Bank\Bank;
use GamePlatform\Domain\Admin\Bank\Exception\BankingException;
use GamePlatform\Framework\Uuid\Uuid;
use GamePlatform\Testing\Traits\UsesContainer;
use GamePlatform\Testing\Traits\UsesTestRedisDatabase;
use Interop\Container\ContainerInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Predis\Client;

class RedisBankIntegrationTest extends TestCase
{
    use UsesContainer,
        UsesTestRedisDatabase;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Client */
    private $client;
    /** @var  Bank */
    private $bank;

    public function setUp()
    {
        $this->container  = $this->setRedisDatabase($this->createContainer());
        $this->container->get(Config::class)->set('admin.bank.driver', 'redis');
        $this->client = $this->container->get(Client::class);
        $this->bank = $this->container->get(Bank::class);
    }

    public function test_deposit_adds_record_to_database()
    {
        $this->bank->deposit($id = Uuid::generate(), new Money(1000, new Currency('GBP')));

        $value = $this->client->get((string) $id);

        $this->assertEquals('{"amount":"1000","currency":"GBP"}', $value);
    }

    public function test_exception_is_thrown_if_record_exists_for_game_id()
    {
        $this->bank->deposit($id = Uuid::generate(), new Money(1000, new Currency('GBP')));

        $this->expectException(BankingException::class);
        $this->expectExceptionMessage("Record for Game {$id} already exists");

        $this->bank->deposit($id, new Money(1000, new Currency('GBP')));
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->flushRedisDatabase($this->container);
    }
}
