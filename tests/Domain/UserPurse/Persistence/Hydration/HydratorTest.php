<?php

namespace BackToWin\Domain\UserPurse\Persistence\Hydration;

use BackToWin\Framework\Calculation\Calculation;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_converts_scalar_object_into_user_purse_object()
    {
        $purse = Hydrator::hydratePurse(
            (object) [
                'user_id' => (new Uuid('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21'))->toBinary(),
                'currency' => 'GBP',
                'amount' => 5000,
                'created_at' => 1531699200,
                'updated_at' => 1531699200
            ]
        );

        $this->assertEquals('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21', $purse->getUserId());
        $this->assertEquals(new Money(5000, new Currency('GBP')), $purse->getTotal());
        $this->assertEquals(new \DateTimeImmutable('2018-07-16 00:00:00'), $purse->getCreatedDate());
        $this->assertEquals(new \DateTimeImmutable('2018-07-16 00:00:00'), $purse->getLastModifiedDate());
    }

    public function test_converts_scalar_object_into_a_user_purse_transaction_object()
    {
        $transaction = Hydrator::hydrateTransaction(
            (object) [
                'id' => (new Uuid('f07dc671-63d5-4908-8898-6e138a34b221'))->toBinary(),
                'user_id' => (new Uuid('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21'))->toBinary(),
                'currency' => 'GBP',
                'amount' => 5000,
                'calculation' => 'ADD',
                'description' => null,
                'timestamp' => 1531699200,
            ]
        );

        $this->assertEquals('f07dc671-63d5-4908-8898-6e138a34b221', $transaction->getId());
        $this->assertEquals('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21', $transaction->getUserId());
        $this->assertEquals(new Money(5000, new Currency('GBP')), $transaction->getTotal());
        $this->assertEquals(Calculation::ADD(), $transaction->getCalculation());
        $this->assertNull($transaction->getDescription());
        $this->assertEquals(new \DateTimeImmutable('2018-07-16 00:00:00'), $transaction->getCreatedDate());
    }
}
