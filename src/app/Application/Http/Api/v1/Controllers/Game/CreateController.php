<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Game;

use BackToWin\Boundary\Game\Command\CreateGameCommand;
use BackToWin\Framework\Controller\ControllerService;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendFailResponse;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class CreateController
{
    use ControllerService;

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        if (!$body) {
            return new JsendFailResponse([
                new JsendError('Unable to parse request body')
            ]);
        }

        try {
            $command = $this->hydrateCommand($body);
        } catch (\UnexpectedValueException | \InvalidArgumentException $e) {
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
            $data->status,
            $data->currency,
            $data->max,
            $data->min,
            $data->start,
            $data->players
        );
    }
}
