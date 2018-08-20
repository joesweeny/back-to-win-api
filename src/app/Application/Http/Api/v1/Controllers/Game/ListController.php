<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Game;

use GamePlatform\Boundary\Game\Command\ListGamesCommand;
use GamePlatform\Framework\Controller\ControllerService;
use GamePlatform\Framework\Jsend\JsendSuccessResponse;

class ListController
{
    use ControllerService;

    public function __invoke()
    {
        return new JsendSuccessResponse([
            'games' => $this->bus->execute(new ListGamesCommand())
        ]);
    }
}
