<?php

namespace BackToWin\Application\Http\Api\v1\Routing\Game;

use BackToWin\Application\Http\Api\v1\Controllers\Game\CreateController;
use BackToWin\Framework\Routing\RouteMapper;
use FastRoute\RouteCollector;

class RouteManager implements RouteMapper
{
    /**
     * @inheritdoc
     */
    public function map(RouteCollector $router)
    {
        $router->addRoute('POST', '/api/game', CreateController::class);
    }
}
