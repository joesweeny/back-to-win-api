<?php

namespace BackToWin\Boundary\Game;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class GamePresenterTest extends TestCase
{
    public function test_to_dto_converts_a_game_object_into_a_scalar_data_transfer_object()
    {
        $game = new Game(
            new Uuid('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2'),
            GameType::GENERAL_KNOWLEDGE(),
            GameStatus::CREATED(),
            new Money(50, new Currency('GBP')),
            new Money(10, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            4
        );

        $game->setCreatedDate(new \DateTimeImmutable('2018-07-18 00:00:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2018-07-18 00:00:00'));

        $dto = (new GamePresenter())->toDto($game);

        $this->assertEquals('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2', $dto->id);
        $this->assertEquals('GENERAL_KNOWLEDGE', $dto->type);
        $this->assertEquals('CREATED', $dto->status);
        $this->assertEquals('GBP', $dto->currency);
        $this->assertEquals(50, $dto->max);
        $this->assertEquals(10, $dto->min);
        $this->assertEquals(4, $dto->players);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $dto->start);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $dto->created_at);
        $this->assertEquals('2018-07-18T00:00:00+00:00', $dto->updated_at);
    }
}
