<?php

namespace GamePlatform\Application\Http\Api\v1\Routing\OpenApi;

use FastRoute\RouteCollector;
use GamePlatform\Application\Http\Api\v1\Controllers\OpenApi\OpenApiController;
use GamePlatform\Framework\Routing\RouteMapper;

class RouteManager implements RouteMapper
{
    /**
     * @inheritdoc
     */
    public function map(RouteCollector $router)
    {
        $router->get('/', OpenApiController::class);
        $router->get('/open-api.json', OpenApiController::class.'@spec');
    }
}
