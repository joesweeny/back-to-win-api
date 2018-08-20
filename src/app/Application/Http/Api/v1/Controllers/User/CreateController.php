<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\User;

use GamePlatform\Boundary\User\Command\CreateUserCommand;
use GamePlatform\Framework\Exception\UserCreationException;
use GamePlatform\Framework\Jsend\JsendError;
use GamePlatform\Framework\Jsend\JsendFailResponse;
use GamePlatform\Framework\Jsend\JsendResponse;
use GamePlatform\Framework\Jsend\JsendSuccessResponse;
use Chief\CommandBus;
use GamePlatform\Application\Http\Api\v1\Validation\User\RequestValidator;
use Psr\Http\Message\ServerRequestInterface;

class CreateController
{
    /**
     * @var CommandBus
     */
    private $bus;
    /**
     * @var RequestValidator
     */
    private $validator;

    public function __construct(CommandBus $bus, RequestValidator $validator)
    {
        $this->bus = $bus;
        $this->validator = $validator;
    }

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     * @throws \InvalidArgumentException
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        if (!$body) {
            return new JsendFailResponse([
                new JsendError('Unable to parse request body')
            ]);
        }

        $errors = $this->validator->validate($body);

        if (!empty($errors)) {
            return new JsendFailResponse(
                array_map(function (string $error) {
                    return new JsendError($error);
                }, $errors)
            );
        }

        try {
            $user = $this->bus->execute($this->hydrateCommand($body));

            return new JsendSuccessResponse([
                'user' => $user
            ]);
        } catch (UserCreationException $e) {
            return (new JsendFailResponse([
                new JsendError($e->getMessage())
            ]))->withStatus(422);
        }
    }

    private function hydrateCommand(\stdClass $data): CreateUserCommand
    {
        return new CreateUserCommand($data->username, $data->email, $data->password);
    }
}
