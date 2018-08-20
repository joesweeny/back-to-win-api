<?php

namespace GamePlatform\Domain\UserPurse\Persistence\Hydration;

use GamePlatform\Domain\UserPurse\Entity\UserPurse;
use GamePlatform\Domain\UserPurse\Entity\UserPurseTransaction;
use GamePlatform\Framework\Calculation\Calculation;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_converts_user_purse_object_into_a_scalar_object()
    {
        $data = Extractor::purseToRawData(
            (new UserPurse(
                new Uuid('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21'),
                new Money(500, new Currency('GBP'))
            ))->setCreatedDate(new \DateTimeImmutable('2018-07-16 00:00:00'))
                ->setLastModifiedDate(new \DateTimeImmutable('2018-07-16 00:00:00'))
        );

        $this->assertEquals('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21', Uuid::createFromBinary($data->user_id));
        $this->assertEquals('GBP', $data->currency);
        $this->assertEquals(500, $data->amount);
        $this->assertEquals(1531699200, $data->created_at);
        $this->assertEquals(1531699200, $data->updated_at);
    }

    public function test_converts_user_purse_transaction_into_scalar_object()
    {
        $data = Extractor::transactionToRawData(
            (new UserPurseTransaction('f07dc671-63d5-4908-8898-6e138a34b221'))
                ->setUserId(new Uuid('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21'))
                ->setTotal(new Money(500, new Currency('GBP')))
                ->setCalculation(Calculation::ADD())
                ->setDescription('Payment to customer')
                ->setCreatedDate(new \DateTimeImmutable('2018-07-16 00:00:00'))
        );

        $this->assertEquals('f07dc671-63d5-4908-8898-6e138a34b221', Uuid::createFromBinary($data->id));
        $this->assertEquals('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21', Uuid::createFromBinary($data->user_id));
        $this->assertEquals('GBP', $data->currency);
        $this->assertEquals(500, $data->amount);
        $this->assertEquals('ADD', $data->calculation);
        $this->assertEquals('Payment to customer', $data->description);
        $this->assertEquals(1531699200, $data->timestamp);
    }
}
