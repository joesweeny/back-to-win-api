<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\User;

use BackToWin\Framework\Controller\ControllerService;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Jsend\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;

class CreateController
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
            'username' => $body->username,
            'first_name' => $body->first_name,
            'last_name' => $body->last_name,
            'location' => $body->location,
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
