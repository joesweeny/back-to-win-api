<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Game;

use Chief\CommandBus;
use GamePlatform\Boundary\Game\Command\EnterGameCommand;
use GamePlatform\Domain\GameEntry\Exception\GameEntryException;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Jsend\JsendError;
use GamePlatform\Framework\Jsend\JsendFailResponse;
use GamePlatform\Framework\Jsend\JsendResponse;
use GamePlatform\Framework\Jsend\JsendSuccessResponse;

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
