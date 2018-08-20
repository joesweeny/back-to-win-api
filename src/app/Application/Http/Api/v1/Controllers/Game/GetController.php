<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Game;

use GamePlatform\Boundary\Game\Command\GetByIdCommand;
use GamePlatform\Framework\Controller\ControllerService;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Jsend\JsendError;
use GamePlatform\Framework\Jsend\JsendFailResponse;
use GamePlatform\Framework\Jsend\JsendResponse;
use GamePlatform\Framework\Jsend\JsendSuccessResponse;
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
