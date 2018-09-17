<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Game;

use Chief\CommandBus;
use GamePlatform\Application\Http\Api\v1\Validation\Game\RequestValidator;
use GamePlatform\Boundary\Game\Command\SettleGameCommand;
use GamePlatform\Domain\Game\Exception\GameSettlementException;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Jsend\JsendError;
use GamePlatform\Framework\Jsend\JsendFailResponse;
use GamePlatform\Framework\Jsend\JsendResponse;
use GamePlatform\Framework\Jsend\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class SettleController
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

        $errors = $this->validator->validateSettle($body);

        if (!empty($errors)) {
            return new JsendFailResponse(
                array_map(function (string $error) {
                    return new JsendError($error);
                }, $errors)
            );
        }

        try {
            $this->bus->execute(
                new SettleGameCommand(
                    $body->game_id,
                    $body->user_id,
                    $body->currency,
                    $body->amount
                )
            );

            return new JsendSuccessResponse();
        } catch (NotFoundException | \InvalidArgumentException $e) {
            return (new JsendFailResponse([
                new JsendError($e->getMessage())
            ]))->withStatus(404);
        } catch (GameSettlementException $e) {
            return (new JsendFailResponse([
                new JsendError($e->getMessage())
            ]))->withStatus(422);
        }
    }
}
