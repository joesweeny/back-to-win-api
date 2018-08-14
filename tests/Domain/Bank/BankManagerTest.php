<?php

namespace BackToWin\Domain\Bank;

use BackToWin\Domain\Bank\Exception\BankingException;
use BackToWin\Domain\User\Entity\User;
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
}
