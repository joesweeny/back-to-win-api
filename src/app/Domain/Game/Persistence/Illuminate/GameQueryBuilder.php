<?php

namespace GamePlatform\Domain\Game\Persistence\Illuminate;

use GamePlatform\Domain\Game\Persistence\GameRepositoryQuery;
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

        return $builder;
    }
}
