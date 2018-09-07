<?php

namespace GamePlatform\Domain\Bank;

use GamePlatform\Domain\Bank\Exception\BankingException;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class BankManagerTest extends TestCase
{
    /** @var  Bank */
    private $bank;
    /** @var  BankManager */
    private $manager;

    public function setUp()
    {
        $this->bank = $this->prophesize(Bank::class);
        $this->manager = new BankManager($this->bank->reveal());
    }

    public function test_money_is_withdrawn_if_user_has_sufficient_funds()
    {
        $user = new User('57f08f28-dc80-4adb-bc6b-1cfff1b73d6c');

        $this->bank->getBalance($user->getId())->willReturn(new Money(1000, new Currency('GBP')));

        $this->bank->withdraw($user->getId(), new Money(500, new Currency('GBP')))->willReturn(
            $withdrawn = new Money(500, new Currency('GBP'))
        );

        $this->manager->withdraw($user, new Money(500, new Currency('GBP')));

        $this->addToAssertionCount(1);
    }

    public function test_exception_is_thrown_if_user_has_insufficient_funds()
    {
        $user = new User('57f08f28-dc80-4adb-bc6b-1cfff1b73d6c');

        $this->bank->getBalance($user->getId())->willReturn(new Money(10, new Currency('GBP')));

        $this->expectException(BankingException::class);
        $this->expectExceptionMessage(
            'Cannot withdraw money for User 57f08f28-dc80-4adb-bc6b-1cfff1b73d6c due to insufficient funds'
        );

        $this->manager->withdraw($user, new Money(500, new Currency('GBP')));
    }

    public function test_a_bank_account_is_opened_for_a_user_if_one_does_not_already_exist()
    {
        $this->bank->openAccount($id = Uuid::generate(), $money = new Money(500, new Currency('GBP')))->shouldBeCalled();

        $this->manager->openAccount($id, $money);

        $this->addToAssertionCount(1);
    }

    public function test_exception_is_thrown_if_opening_an_account_for_a_user_that_already_has_an_account()
    {
        $this->bank->openAccount($id = Uuid::generate(), $money = new Money(500, new Currency('GBP')))->willThrow(
            new BankingException('User has an open account')
        );

        $this->expectException(BankingException::class);
        $this->manager->openAccount($id, $money);
    }
}
