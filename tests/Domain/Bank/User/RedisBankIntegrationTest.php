<?php

namespace BackToWin\Domain\Bank\User;

use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\UsesContainer;
use BackToWin\Testing\Traits\UsesTestRedisDatabase;
use Interop\Container\ContainerInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class RedisBankIntegrationTest extends TestCase
{
    use UsesContainer,
        UsesTestRedisDatabase;

    /** @var  RedisBank */
    private $bank;
    /** @var  ContainerInterface */
    private $container;

    public function setUp()
    {
        $this->container  = $this->setRedisDatabase($this->createContainer());
        $this->bank = $this->container->get(RedisBank::class);
    }

    public function test_bank_account_can_be_opened_and_balance_retrievd()
    {
        $this->bank->openAccount($id = Uuid::generate(), new Money(1000, new Currency('GBP')));

        $balance = $this->bank->getBalance($id);

        $this->assertEquals(new Money(1000, new Currency('GBP')), $balance);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->flushRedisDatabase($this->container);
    }
}
