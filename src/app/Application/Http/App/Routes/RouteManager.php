<?php

namespace BackToWin\Application\Http\App\Routes;

use FastRoute\RouteCollector;
use BackToWin\Application\Http\App\Controllers\HomepageController;
use BackToWin\Framework\Routing\RouteMapper;

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
