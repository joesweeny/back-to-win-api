<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Auth;

use Chief\CommandBus;
use BackToWin\Boundary\Auth\Command\GenerateTokenCommand;
use BackToWin\Boundary\User\Command\VerifyUserCommand;
use BackToWin\Framework\Exception\NotAuthenticatedException;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendFailResponse;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class TokenController
{
    /**
     * @var CommandBus
     */
    private $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        if (!$body) {
            return $this->failResponse('Unable to parse request body');
        }

        if (!$body->email) {
            return $this->failResponse("Request body is missing 'email' parameter");
        }

        if (!$body->password) {
            return $this->failResponse("Request body is missing 'password' parameter");
        }

        try {
            $userId = $this->bus->execute(new VerifyUserCommand($body->email, $body->password));

            return new JsendSuccessResponse([
                'token' => $this->bus->execute(new GenerateTokenCommand($userId))
            ]);
        } catch (NotAuthenticatedException | NotFoundException $e) {
            return $this->failResponse('Unable to generate token with credentials provided', 403);
        }
    }

    private function failResponse(string $message, int $code = 400): JsendFailResponse
    {
        return (new JsendFailResponse([new JsendError($message)]))->withStatus($code);
    }
}
