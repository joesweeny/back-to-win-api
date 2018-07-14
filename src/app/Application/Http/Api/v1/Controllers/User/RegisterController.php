<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\Session\Command\CreateSessionTokenCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UserEmailValidationException;
use Cocktales\Boundary\User\Command\RegisterUserCommand;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class RegisterController
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     * @throws \InvalidArgumentException
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $data = (object) [
            'email' => $body->email,
            'password' => $body->password
        ];

        try {
            $user = $this->bus->execute(new RegisterUserCommand($data));
            $token = $this->bus->execute(new CreateSessionTokenCommand($user->id));

            return new JsendSuccessResponse([
                'user' => $user,
                'token' => $token
            ]);
        } catch (UserEmailValidationException $e) {
            return (new JsendFailResponse([
                new JsendError('A user has already registered with this email address')
            ]))->withStatus(422);
        } catch (NotFoundException $e) {
            return (new JsendFailResponse([
                new JsendError('Unable to verify user credentials')
            ]))->withStatus(401);
        }
    }
}
