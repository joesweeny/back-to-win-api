<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Game;

use BackToWin\Boundary\Game\Command\ListGamesCommand;
use BackToWin\Framework\Controller\ControllerService;
use BackToWin\Framework\Jsend\JsendSuccessResponse;

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
