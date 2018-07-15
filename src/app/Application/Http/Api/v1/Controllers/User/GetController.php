<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\User;

use BackToWin\Boundary\User\Command\GetUserByIdCommand;
use BackToWin\Framework\Controller\ControllerService;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendFailResponse;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class GetController
{
    use ControllerService;

    /**
     * @param string $id
     * @param ServerRequestInterface $request
     * @return JsendResponse
     */
    public function __invoke(string $id, ServerRequestInterface $request): JsendResponse
    {
        try {
            $user = $this->bus->execute(new GetUserByIdCommand($id));

            return new JsendSuccessResponse([
               'user' => $user
            ]);
        } catch (NotFoundException $e) {
            return (new JsendFailResponse([
                new JsendError("User with ID {$id} does not exist")
            ]))->withStatus(404);
        }
    }
}
