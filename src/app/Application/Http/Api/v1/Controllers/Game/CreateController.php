<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Game;

use BackToWin\Application\Http\Api\v1\Validation\Game\RequestValidator;
use BackToWin\Boundary\Game\Command\CreateGameCommand;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendFailResponse;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;
use Chief\CommandBus;
use Psr\Http\Message\ServerRequestInterface;

class CreateController
{
    /**
     * @var RequestValidator
     */
    private $validator;
    /**
     * @var CommandBus
     */
    private $bus;

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

        return new JsendSuccessResponse([
            'game' => $this->bus->execute($command)
        ]);
    }

    /**
     * @param \stdClass $data
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return CreateGameCommand
     */
    private function hydrateCommand(\stdClass $data): CreateGameCommand
    {
        return new CreateGameCommand(
            $data->type,
            $data->currency,
            $data->max,
            $data->min,
            $data->start,
            $data->players
        );
    }
}
