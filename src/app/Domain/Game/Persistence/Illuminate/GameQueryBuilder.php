<?php

namespace BackToWin\Domain\Game\Persistence\Illuminate;

use BackToWin\Domain\Game\Persistence\GameRepositoryQuery;
use Illuminate\Database\Query\Builder;

class GameQueryBuilder
{
    public function build(Builder $builder, GameRepositoryQuery $query = null): Builder
    {
        $query = $query ?: new GameRepositoryQuery();

        if (($status = $query->getWhereStatusEquals()) !== null) {
            $builder->where('status', $status->getValue());
        }

        if (($type = $query->getWhereTypeEquals()) !== null) {
            $builder->where('type', $type->getValue());
        }

        return $builder;
    }
}
