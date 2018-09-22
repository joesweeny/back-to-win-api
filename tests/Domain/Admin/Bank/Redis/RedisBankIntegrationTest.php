<?php

namespace BackToWin\Domain\Admin\Bank\Redis;

use BackToWin\Bootstrap\Config;
use BackToWin\Domain\Admin\Bank\Bank;
use BackToWin\Domain\Admin\Bank\Exception\BankingException;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\UsesContainer;
use BackToWin\Testing\Traits\UsesTestRedisDatabase;
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
        $this->container->get(Config::class)->set('bank.admin.driver', 'redis');
        $this->bank = $this->container->get(Bank::class);
        $this->client = $this->container->get(Client::class);
    }

    public function test_deposit_adds_record_to_database()
    {
        $this->bank->deposit($id = Uuid::generate(), new Money(1000, new Currency('FAKE')));

        $value = $this->client->get('admin-bank:' . (string) $id);

        $this->assertEquals('{"amount":"1000","currency":"FAKE"}', $value);
    }

    public function test_exception_is_thrown_if_record_exists_for_game_id()
    {
        $this->bank->deposit($id = Uuid::generate(), new Money(1000, new Currency('FAKE')));

        $this->expectException(BankingException::class);
        $this->expectExceptionMessage("Record for Game {$id} already exists");

        $this->bank->deposit($id, new Money(1000, new Currency('FAKE')));
    }

    public function test_get_balance_returns_a_total_of_all_funds_deposited_via_the_bank()
    {
        $this->client->set(
            (string) Uuid::generate(),
            json_encode((new Money(100, new Currency('FAKE')))->jsonSerialize())
        );

        for ($i = 0; $i < 4; $i++) {
            $this->bank->deposit(Uuid::generate(), new Money(1000, new Currency('FAKE')));
        }

        $balance = $this->bank->getBalance();

        $this->assertEquals(new Money(4000, new Currency('FAKE')), $balance);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->flushRedisDatabase($this->container);
    }
}
