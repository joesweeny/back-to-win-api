<?php

namespace GamePlatform\Application\Http\Api\v1\Routing\Avatar;

use FastRoute\RouteCollector;
use GamePlatform\Application\Http\Api\v1\Controllers\Avatar\AddController;
use GamePlatform\Framework\Routing\RouteMapper;

class RouteManager implements RouteMapper
{
    /**
     * @inheritdoc
     */
    public function map(RouteCollector $router): void
    {
        $router->addRoute('PUT', '/api/avatar', AddController::class);
    }
}
