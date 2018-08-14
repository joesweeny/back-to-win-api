<?php

namespace BackToWin\Domain\Bank\User;

use BackToWin\Bootstrap\Config;
use BackToWin\Domain\Bank\Bank;
use BackToWin\Domain\Bank\Exception\BankingException;
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
        $this->container->get(Config::class)->set('bank.bank-driver', 'redis');
        $this->bank = $this->container->get(Bank::class);
    }

    public function test_bank_account_can_be_opened_and_balance_retrievd()
    {
        $this->bank->openAccount($id = Uuid::generate(), new Money(1000, new Currency('GBP')));

        $balance = $this->bank->getBalance($id);

        $this->assertEquals(new Money(1000, new Currency('GBP')), $balance);
    }

    public function test_deposit_updates_existing_balance_in_account()
    {
        $this->bank->openAccount($id = Uuid::generate(), new Money(1000, new Currency('GBP')));

        $this->bank->deposit($id, new Money(5500, new Currency('GBP')));

        $balance = $this->bank->getBalance($id);

        $this->assertEquals(new Money(6500, new Currency('GBP')), $balance);
    }

    public function test_withdraw_updates_balance_in_account()
    {
        $this->bank->openAccount($id = Uuid::generate(), new Money(1000, new Currency('GBP')));

        $this->bank->withdraw($id, new Money(500, new Currency('GBP')));

        $balance = $this->bank->getBalance($id);

        $this->assertEquals(new Money(500, new Currency('GBP')), $balance);
    }

    public function test_exception_is_thrown_if_attempting_to_deposit_money_with_different_currency()
    {
        $this->bank->openAccount($id = Uuid::generate(), new Money(1000, new Currency('GBP')));

        $this->expectException(BankingException::class);
        $this->expectExceptionMessage(
            "Cannot deposit money as account currency does not match deposit currency for User {$id}"
        );
        $this->bank->deposit($id, new Money(5500, new Currency('EUR')));
    }

    public function test_exception_is_thrown_if_attempting_to_withdraw_money_with_different_currency()
    {
        $this->bank->openAccount($id = Uuid::generate(), new Money(1000, new Currency('GBP')));

        $this->expectException(BankingException::class);
        $this->expectExceptionMessage(
            "Cannot withdraw money as account currency does not match deposit currency for User {$id}"
        );
        $this->bank->withdraw($id, new Money(5500, new Currency('EUR')));
    }

    public function test_withdraw_money_with_insufficient_funds_puts_account_in_negative_balance()
    {
        $this->bank->openAccount($id = Uuid::generate(), new Money(1000, new Currency('GBP')));

        $this->bank->withdraw($id, new Money(5500, new Currency('GBP')));

        $balance = $this->bank->getBalance($id);

        $this->assertEquals(new Money(-4500, new Currency('GBP')), $balance);

    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->flushRedisDatabase($this->container);
    }
}
