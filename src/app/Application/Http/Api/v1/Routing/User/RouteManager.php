<?php

namespace GamePlatform\Application\Http\Api\v1\Routing\User;

use GamePlatform\Application\Http\Api\v1\Controllers\User\CreateController;
use GamePlatform\Application\Http\Api\v1\Controllers\User\GetController;
use GamePlatform\Application\Http\Api\v1\Controllers\User\ListController;
use GamePlatform\Framework\Routing\RouteMapper;
use FastRoute\RouteCollector;

class RouteManager implements RouteMapper
{
    /**
     * @param RouteCollector $router
     * @return void
     */
    public function map(RouteCollector $router)
    {
        $router->addRoute('POST', '/api/user', CreateController::class);
        $router->addRoute('GET', '/api/user/{id}', GetController::class);
        $router->addRoute('GET', '/api/user', ListController::class);
    }
}
