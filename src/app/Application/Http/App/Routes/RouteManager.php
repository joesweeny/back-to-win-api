<?php

namespace GamePlatform\Application\Http\App\Routes;

use FastRoute\RouteCollector;
use GamePlatform\Application\Http\App\Controllers\HomepageController;
use GamePlatform\Framework\Routing\RouteMapper;

class RouteManager implements RouteMapper
{
    /**
     * @param RouteCollector $router
     * @return void
     */
    public function map(RouteCollector $router)
    {
        $router->addRoute('GET', '/', HomepageController::class);
    }
}
