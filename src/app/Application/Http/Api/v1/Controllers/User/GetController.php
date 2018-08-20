<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\User;

use GamePlatform\Boundary\User\Command\GetUserByIdCommand;
use GamePlatform\Framework\Controller\ControllerService;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Jsend\JsendError;
use GamePlatform\Framework\Jsend\JsendFailResponse;
use GamePlatform\Framework\Jsend\JsendResponse;
use GamePlatform\Framework\Jsend\JsendSuccessResponse;
use Ramsey\Uuid\Exception\InvalidUuidStringException;

class GetController
{
    use ControllerService;

    /**
     * @param string $id
     * @return JsendResponse
     */
    public function __invoke(string $id): JsendResponse
    {
        try {
            $user = $this->bus->execute(new GetUserByIdCommand($id));

            return new JsendSuccessResponse([
               'user' => $user
            ]);
        } catch (NotFoundException | InvalidUuidStringException $e) {
            return (new JsendFailResponse([
                new JsendError("User with ID {$id} does not exist")
            ]))->withStatus(404);
        }
    }
}
