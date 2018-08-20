<?php

namespace GamePlatform\Domain\UserPurse\Entity;

use GamePlatform\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class UserPurseTest extends TestCase
{
    public function test_add_funds_updates_total()
    {
        $purse = new UserPurse(Uuid::generate(), new Money(1000, new Currency('GBP')));

        $purse = $purse->addMoney(new Money(5000, new Currency('GBP')));

        $this->assertEquals(new Money(6000, new Currency('GBP')), $purse->getTotal());
    }

    public function test_subtract_funds_updates_total()
    {
        $purse = new UserPurse(Uuid::generate(), new Money(1000, new Currency('GBP')));

        $purse = $purse->subtractMoney(new Money(500, new Currency('GBP')));

        $this->assertEquals(new Money(500, new Currency('GBP')), $purse->getTotal());
    }

    public function test_exception_is_thrown_if_attempting_to_add_funds_with_currency_mismatch()
    {
        $purse = new UserPurse(Uuid::generate(), new Money(1000, new Currency('GBP')));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Currencies must be identical');
        $purse->addMoney(new Money(5000, new Currency('EUR')));
    }

    public function test_exception_is_thrown_if_attempting_to_subtract_funds_with_currency_mismatch()
    {
        $purse = new UserPurse(Uuid::generate(), new Money(1000, new Currency('GBP')));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Currencies must be identical');
        $purse->subtractMoney(new Money(5000, new Currency('EUR')));
    }
}
