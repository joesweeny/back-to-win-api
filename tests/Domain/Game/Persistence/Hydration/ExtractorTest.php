<?php

namespace GamePlatform\Domain\Game\Persistence\Hydration;

use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Enum\GameType;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_converts_game_object_into_scalar_object()
    {
        $game = new Game(
            new Uuid('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2'),
            GameType::GENERAL_KNOWLEDGE(),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP')),
            new Money(50, new Currency('GBP')),
            new Money(10, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            4
        );

        $game->setCreatedDate(new \DateTimeImmutable('2018-07-18 00:00:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2018-07-18 00:00:00'));

        $data = Extractor::toRawData($game);

        $this->assertEquals('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2', Uuid::createFromBinary($data->id));
        $this->assertEquals('GENERAL_KNOWLEDGE', $data->type);
        $this->assertEquals('CREATED', $data->status);
        $this->assertEquals('GBP', $data->currency);
        $this->assertEquals(500, $data->buy_in);
        $this->assertEquals(50, $data->max);
        $this->assertEquals(10, $data->min);
        $this->assertEquals(1531872000, $data->start);
        $this->assertEquals(4, $data->players);
        $this->assertEquals(1531872000, $data->created_at);
        $this->assertEquals(1531872000, $data->updated_at);
    }
}
