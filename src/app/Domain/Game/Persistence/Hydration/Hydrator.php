<?php

namespace GamePlatform\Domain\Game\Persistence\Hydration;

use GamePlatform\Domain\Game\Entity\Game;
use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Enum\GameType;
use GamePlatform\Framework\Uuid\Uuid;
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
            new Money($data->buy_in, new Currency($data->currency)),
            new Money($data->max, new Currency($data->currency)),
            new Money($data->min, new Currency($data->currency)),
            (new \DateTimeImmutable())->setTimestamp($data->start),
            $data->players
        );

        $game->setCreatedDate((new \DateTimeImmutable())->setTimestamp($data->created_at))
            ->setLastModifiedDate((new \DateTimeImmutable())->setTimestamp($data->updated_at));

        return $game;
    }
}
