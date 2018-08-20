<?php

namespace GamePlatform\Application\Http\Api\v1\Routing\UserPurse;

use GamePlatform\Application\Http\Api\v1\Controllers\UserPurse\GetController;
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
        $router->addRoute('GET', '/api/user/{id}/purse', GetController::class);
    }
}
