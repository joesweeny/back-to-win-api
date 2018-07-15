<?php

namespace BackToWin\Application\Http\Api\v1\Routing\User;

use BackToWin\Application\Http\Api\v1\Controllers\User\CreateController;
use BackToWin\Application\Http\Api\v1\Controllers\User\GetController;
use BackToWin\Framework\Routing\RouteMapper;
use FastRoute\RouteCollector;

class RouteManager implements RouteMapper
{
    /**
     * @param RouteCollector $router
     * @return void
     */
    public function map(RouteCollector $router)
    {
        $router->addRoute('POST', '/api/v1/user', CreateController::class);
        $router->addRoute('GET', '/api/v1/user/{id}', GetController::class);
    }
}
