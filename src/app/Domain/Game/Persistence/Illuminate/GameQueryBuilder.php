<?php

namespace BackToWin\Domain\Game\Persistence\Illuminate;

use BackToWin\Domain\Game\Persistence\GameRepositoryQuery;
use Illuminate\Database\Query\Builder;

class GameQueryBuilder
{
    public function build(Builder $builder, GameRepositoryQuery $query = null): Builder
    {
        $query = $query ?: new GameRepositoryQuery();

        if ($query->getWhereStatusEquals() !== null) {
            $builder->where('status', $query->getWhereStatusEquals()->getValue());
        }

        if ($query->getWhereTypeEquals() !== null) {
            $builder->where('type', $query->getWhereTypeEquals()->getValue());
        }

        if ($query->getGameStartsBeforeWhere() !== null) {
            $builder->where('start', '<', $query->getGameStartsBeforeWhere()->getTimestamp());
        }

        if ($query->getCurrencyEqualsWhere() !== null) {
            $builder->where('currency', $query->getCurrencyEqualsWhere()->getCode());
        }

        if ($query->getBuyInLessThanWhere() !== null) {
            $builder->where('buy_in', '<', $query->getBuyInLessThanWhere());
        }

        return $builder;
    }
}
