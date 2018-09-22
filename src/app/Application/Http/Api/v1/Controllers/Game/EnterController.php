<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Game;

use Chief\CommandBus;
use BackToWin\Boundary\Game\Command\EnterGameCommand;
use BackToWin\Domain\GameEntry\Exception\GameEntryException;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendFailResponse;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;

class EnterController
{
    /**
     * @var CommandBus
     */
    private $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(string $gameId, string $userId): JsendResponse
    {
        try {
            $command = new EnterGameCommand($gameId, $userId);
        } catch (\UnexpectedValueException $e) {
            return new JsendFailResponse([
                new JsendError($e->getMessage())
            ]);
        }

        try {
            $this->bus->execute($command);

            return new JsendSuccessResponse();
        } catch (NotFoundException $e) {
            return (new JsendFailResponse([
                new JsendError($e->getMessage())
            ]))->withStatus(404);
        } catch (GameEntryException $e) {
            return (new JsendFailResponse([
                new JsendError($e->getMessage())
            ]))->withStatus(422);
        }
    }
}
