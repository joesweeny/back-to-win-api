<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Game;

use GamePlatform\Boundary\Game\Command\ListGamesCommand;
use GamePlatform\Framework\Controller\ControllerService;
use GamePlatform\Framework\Jsend\JsendError;
use GamePlatform\Framework\Jsend\JsendFailResponse;
use GamePlatform\Framework\Jsend\JsendResponse;
use GamePlatform\Framework\Jsend\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class ListController
{
    use ControllerService;

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $query = $request->getQueryParams();

        try {
            return new JsendSuccessResponse([
                'games' => $this->bus->execute(new ListGamesCommand($query))
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \InvalidArgumentException || $e instanceof \UnexpectedValueException) {
                return new JsendFailResponse([
                    new JsendError($e->getMessage())
                ]);
            }

            return new JsendFailResponse([
                new JsendError('Date provided in not in a valid format')
            ]);
        }
    }
}
