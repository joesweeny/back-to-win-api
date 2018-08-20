<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\User;

use GamePlatform\Boundary\User\Command\ListUsersCommand;
use GamePlatform\Framework\Controller\ControllerService;
use GamePlatform\Framework\Jsend\JsendResponse;
use GamePlatform\Framework\Jsend\JsendSuccessResponse;

class ListController
{
    use ControllerService;

    public function __invoke(): JsendResponse
    {
        return new JsendSuccessResponse([
            'users' => $this->bus->execute(new ListUsersCommand())
        ]);
    }
}
