<?php

namespace BackToWin\Application\Http\Api\v1\Routing\Game;

use BackToWin\Application\Http\Api\v1\Controllers\Game\CreateController;
use BackToWin\Application\Http\Api\v1\Controllers\Game\EnterController;
use BackToWin\Application\Http\Api\v1\Controllers\Game\GetController;
use BackToWin\Application\Http\Api\v1\Controllers\Game\ListController;
use BackToWin\Application\Http\Api\v1\Controllers\Game\SettleController;
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
        $router->addRoute('GET', '/api/game/{id}', GetController::class);
        $router->addRoute('GET', '/api/game', ListController::class);
        $router->addRoute('POST', '/api/game/{gameId}/user/{userId}', EnterController::class);
        $router->addRoute('POST', '/api/game/settle', SettleController::class);
    }
}
