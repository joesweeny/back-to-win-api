<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\UserPurse;

use BackToWin\Boundary\UserPurse\Command\GetUserPurseCommand;
use BackToWin\Framework\Controller\ControllerService;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendFailResponse;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;

class GetController
{
    use ControllerService;

    public function __invoke(string $id): JsendResponse
    {
        try {
            $command = new GetUserPurseCommand($id);
        } catch (\InvalidArgumentException $e) {
            return $this->failResponse($id);
        }

        try {
            return new JsendSuccessResponse([
                'purse' => $this->bus->execute($command)
            ]);
        } catch (NotFoundException $e) {
            return $this->failResponse($id);
        }
    }

    private function failResponse(string $id): JsendFailResponse
    {
        return (new JsendFailResponse([
            new JsendError("Purse for User {$id} does not exist")
        ]))->withStatus(404);
    }
}
