<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\User;

use BackToWin\Boundary\User\Command\ListUsersCommand;
use BackToWin\Framework\Controller\ControllerService;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;

class ListController
{
    use ControllerService;

    public function __invoke(): JsendResponse
    {
        return new JsendSuccessResponse([
            'user' => $this->bus->execute(new ListUsersCommand())
        ]);
    }
}
