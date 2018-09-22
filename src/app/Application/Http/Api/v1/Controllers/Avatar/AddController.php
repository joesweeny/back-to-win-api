<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Avatar;

use Chief\CommandBus;
use BackToWin\Application\Http\Api\v1\Validation\Avatar\RequestValidator;
use BackToWin\Boundary\Avatar\Command\AddAvatarCommand;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendErrorResponse;
use BackToWin\Framework\Jsend\JsendFailResponse;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class AddController
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
            $command = $this->hydrateCommand($body);
        } catch (\UnexpectedValueException $e) {
            return new JsendFailResponse([
                new JsendError($e->getMessage())
            ]);
        }

        if ($this->bus->execute($command)) {
            return new JsendSuccessResponse();
        }

        return new JsendErrorResponse([
            new JsendError('Unable to persist Avatar due to server error')
        ]);
    }

    /**
     * @param \stdClass $data
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return AddAvatarCommand
     */
    private function hydrateCommand(\stdClass $data): AddAvatarCommand
    {
        return new AddAvatarCommand(
            $data->user_id,
            $data->filename,
            base64_decode($data->contents) ?: ''
        );
    }
}
