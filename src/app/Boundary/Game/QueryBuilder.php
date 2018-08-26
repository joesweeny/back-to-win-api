<?php

namespace GamePlatform\Boundary\Game;

use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Persistence\GameRepositoryQuery;
use Money\Currency;

class QueryBuilder
{
    /**
     * @param array $parameters
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return GameRepositoryQuery
     */
    public function buildGameRepositoryQuery(array $parameters): GameRepositoryQuery
    {
        $query = new GameRepositoryQuery();

        if ($parameters['status'] !== null) {
            $query->whereStatusEquals(new GameStatus($parameters['status']));
        }

        if ($parameters['start'] !== null) {
            $query->whereGameStartsBefore(new \DateTimeImmutable($parameters['start']));
        }

        if ($parameters['currency'] !== null) {
            $query->whereCurrencyEquals(new Currency($parameters['currency']));
        }

        if ($parameters['buy_in'] !== null) {
            if (!is_numeric($buyIn = $parameters['buy_in'])) {
                throw new \InvalidArgumentException("Parameter 'buy_in' needs to be numeric");
            }

            $query->whereBuyInLessThan($buyIn);
        }

        return $query;
    }
}
