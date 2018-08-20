<?php

namespace GamePlatform\Application\Http\Api\v1\Routing\Game;

use GamePlatform\Application\Http\Api\v1\Controllers\Game\CreateController;
use GamePlatform\Application\Http\Api\v1\Controllers\Game\GetController;
use GamePlatform\Application\Http\Api\v1\Controllers\Game\ListController;
use GamePlatform\Framework\Routing\RouteMapper;
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
    }
}
