<?php

namespace BackToWin\Domain\Game\Persistence\Hydration;

use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_converts_scalar_object_into_game_object()
    {
        $game = Hydrator::fromRawData(
            (object) [
                'id' => (new Uuid('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2'))->toBinary(),
                'type' => 'GENERAL_KNOWLEDGE',
                'status' => 'IN_PLAY',
                'currency' => 'GBP',
                'buy_in' => 500,
                'max' => 50,
                'min' => 10,
                'start' => 1531872000,
                'players' => 4,
                'created_at' => 1531872000,
                'updated_at' => 1531872000,
            ]
        );

        $this->assertEquals('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2', $game->getId());
        $this->assertEquals(GameType::GENERAL_KNOWLEDGE(), $game->getType());
        $this->assertEquals(GameStatus::IN_PLAY(), $game->getStatus());
        $this->assertEquals(new Money(500, new Currency('GBP')), $game->getBuyIn());
        $this->assertEquals(new Money(50, new Currency('GBP')), $game->getMax());
        $this->assertEquals(new Money(10, new Currency('GBP')), $game->getMin());
        $this->assertEquals(new \DateTimeImmutable('2018-07-18 00:00:00'), $game->getStartDateTime());
        $this->assertEquals(4, $game->getPlayers());
        $this->assertEquals(new \DateTimeImmutable('2018-07-18 00:00:00'), $game->getCreatedDate());
        $this->assertEquals(new \DateTimeImmutable('2018-07-18 00:00:00'), $game->getLastModifiedDate());
    }
}
