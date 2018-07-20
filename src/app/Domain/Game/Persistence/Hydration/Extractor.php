<?php

namespace BackToWin\Domain\Game\Persistence\Hydration;

use BackToWin\Domain\Game\Entity\Game;

class Extractor
{
    /**
     * Convert a Game object into a scalar object
     *
     * @param Game $game
     * @return \stdClass
     */
    public static function toRawData(Game $game): \stdClass
    {
        return (object) [
            'id' => $game->getId()->toBinary(),
            'type' => $game->getType()->getValue(),
            'currency' => $game->getMax()->getCurrency()->getCode(),
            'max' => (int) $game->getMax()->getAmount(),
            'min' => (int) $game->getMin()->getAmount(),
            'status' => $game->getStatus()->getValue(),
            'start' => $game->getStartDateTime()->getTimestamp(),
            'players' => $game->getPlayers(),
            'created_at' => $game->getCreatedDate()->getTimestamp(),
            'updated_at' => $game->getLastModifiedDate()->getTimestamp()
        ];
    }
}
