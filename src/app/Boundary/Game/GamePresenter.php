<?php

namespace BackToWin\Boundary\Game;

use BackToWin\Domain\Game\Entity\Game;

class GamePresenter
{
    /**
     * Convert a domain Game object into a scalar data transfer object
     *
     * @param Game $game
     * @return \stdClass
     */
    public function toDto(Game $game): \stdClass
    {
        return (object) [
            'id' => (string) $game->getId(),
            'type' => $game->getType()->getValue(),
            'status' => $game->getStatus()->getValue(),
            'currency' => $game->getMax()->getCurrency()->getCode(),
            'buy_in' => (int) $game->getBuyIn()->getAmount(),
            'max' => (int) $game->getMax()->getAmount(),
            'min' => (int) $game->getMin()->getAmount(),
            'start' => $game->getStartDateTime()->format(\DATE_ATOM),
            'players' => $game->getPlayers(),
            'created_at' => $game->getCreatedDate()->format(\DATE_ATOM),
            'updated_at' => $game->getLastModifiedDate()->format(\DATE_ATOM)
        ];
    }
}
