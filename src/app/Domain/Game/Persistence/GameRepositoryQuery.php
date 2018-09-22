<?php

namespace BackToWin\Domain\Game\Persistence;

use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Framework\Entity\PrivateAttributesTrait;
use Money\Currency;

class GameRepositoryQuery
{
    use PrivateAttributesTrait;

    public function whereStatusEquals(GameStatus $status): self
    {
        return $this->set('where_status_equals', $status);
    }

    public function getWhereStatusEquals(): ?GameStatus
    {
        return $this->get('where_status_equals');
    }

    public function whereTypeEquals(GameType $type): self
    {
        return $this->set('where_type_equals', $type);
    }

    public function getWhereTypeEquals(): ?GameType
    {
        return $this->get('where_type_equals');
    }

    public function whereGameStartsBefore(\DateTimeImmutable $date): self
    {
        return $this->set('where_game_starts_before', $date);
    }

    public function getGameStartsBeforeWhere(): ?\DateTimeImmutable
    {
        return $this->get('where_game_starts_before');
    }

    public function whereCurrencyEquals(Currency $currency): self
    {
        return $this->set('where_currency_equals', $currency);
    }

    public function getCurrencyEqualsWhere(): ?Currency
    {
        return $this->get('where_currency_equals');
    }

    public function whereBuyInLessThan(int $amount): self
    {
        return $this->set('where_buy_in_less_than', $amount);
    }

    public function getBuyInLessThanWhere(): ?int
    {
        return $this->get('where_buy_in_less_than');
    }
}
