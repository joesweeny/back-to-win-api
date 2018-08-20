<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\UserPurse;

use GamePlatform\Boundary\UserPurse\Command\GetUserPurseCommand;
use GamePlatform\Framework\Controller\ControllerService;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Jsend\JsendError;
use GamePlatform\Framework\Jsend\JsendFailResponse;
use GamePlatform\Framework\Jsend\JsendResponse;
use GamePlatform\Framework\Jsend\JsendSuccessResponse;

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
