<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Game;

use BackToWin\Boundary\Game\Command\ListGamesCommand;
use BackToWin\Framework\Controller\ControllerService;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendFailResponse;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;
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
