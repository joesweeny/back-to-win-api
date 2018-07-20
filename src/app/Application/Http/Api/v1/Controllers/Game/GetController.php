<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Game;

use BackToWin\Boundary\Game\Command\GetByIdCommand;
use BackToWin\Framework\Controller\ControllerService;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendFailResponse;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;
use Ramsey\Uuid\Exception\InvalidUuidStringException;

class GetController
{
    use ControllerService;

    public function __invoke(string $id): JsendResponse
    {
        try {
            $game = $this->bus->execute(new GetByIdCommand($id));

            return new JsendSuccessResponse([
                'game' => $game
            ]);
        } catch (NotFoundException | InvalidUuidStringException $e) {
            return (new JsendFailResponse([
                new JsendError("Game with ID {$id} does not exist")
            ]))->withStatus(404);
        }
    }
}
