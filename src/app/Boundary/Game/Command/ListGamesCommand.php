<?php

namespace BackToWin\Boundary\Game\Command;

use Chief\Command;

class ListGamesCommand implements Command
{
    /**
     * @var array
     */
    private $queryParameters;

    public function __construct(array $queryParameters)
    {
        $this->queryParameters = $queryParameters;
    }

    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }
}
