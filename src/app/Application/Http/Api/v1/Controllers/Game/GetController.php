<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Game;

use BackToWin\Boundary\Game\Command\GetByIdCommand;
use BackToWin\Boundary\GameEntry\Command\GetUsersForGameCommand;
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
            try {
                $gameCommand = new GetByIdCommand($id);

                $entriesCommand = new GetUsersForGameCommand($id);
            } catch (\UnexpectedValueException $e) {
                return new JsendFailResponse([
                    new JsendError($e->getMessage())
                ]);
            }

            return new JsendSuccessResponse([
                'game' => $this->bus->execute($gameCommand),
                'users' => $this->bus->execute($entriesCommand)
            ]);
        } catch (NotFoundException | InvalidUuidStringException $e) {
            return (new JsendFailResponse([
                new JsendError("Game with ID {$id} does not exist")
            ]))->withStatus(404);
        }
    }
}
