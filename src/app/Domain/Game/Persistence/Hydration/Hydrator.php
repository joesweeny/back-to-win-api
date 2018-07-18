<?php

namespace BackToWin\Domain\Game\Persistence\Hydration;

use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;

class Hydrator
{
    public static function fromRawData(\stdClass $data): Game
    {
        $game = new Game(
            Uuid::createFromBinary($data->id),
            new GameType($data->type),
            new GameStatus($data->status),
            new Money($data->max, new Currency($data->currency)),
            new Money($data->min, new Currency($data->currency)),
            \DateTimeImmutable::createFromFormat('U', $data->start)
        );

        $game->setCreatedDate(\DateTimeImmutable::createFromFormat('U', $data->created_at))
            ->setLastModifiedDate(\DateTimeImmutable::createFromFormat('U', $data->updated_at));

        return $game;
    }
}
