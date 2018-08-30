<?php

namespace GamePlatform\Application\Http\Api\v1\Routing\Auth;

use FastRoute\RouteCollector;
use GamePlatform\Application\Http\Api\v1\Controllers\Auth\TokenController;
use GamePlatform\Framework\Routing\RouteMapper;

class RouteManager implements RouteMapper
{
    /**
     * @inheritdoc
     */
    public function map(RouteCollector $router)
    {
        $router->addRoute('POST', '/auth/token', TokenController::class);
    }
}