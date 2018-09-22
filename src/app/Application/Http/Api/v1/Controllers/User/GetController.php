<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\User;

use BackToWin\Boundary\User\Command\GetUserByIdCommand;
use BackToWin\Framework\Controller\ControllerService;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendFailResponse;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;
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
