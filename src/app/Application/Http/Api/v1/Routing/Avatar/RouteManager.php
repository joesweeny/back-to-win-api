<?php

namespace BackToWin\Application\Http\Api\v1\Routing\Avatar;

use FastRoute\RouteCollector;
use BackToWin\Application\Http\Api\v1\Controllers\Avatar\AddController;
use BackToWin\Application\Http\Api\v1\Controllers\Avatar\GetController;
use BackToWin\Framework\Routing\RouteMapper;

class RouteManager implements RouteMapper
{
    /**
     * @inheritdoc
     */
    public function map(RouteCollector $router): void
    {
        $router->addRoute('PUT', '/api/avatar', AddController::class);
        $router->addRoute('GET', '/api/avatar/{userId}', GetController::class);
    }
}
