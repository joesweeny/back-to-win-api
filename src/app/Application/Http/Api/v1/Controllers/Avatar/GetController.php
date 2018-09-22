<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Avatar;

use Chief\CommandBus;
use BackToWin\Boundary\Avatar\Command\GetAvatarCommand;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendFailResponse;
use BackToWin\Framework\Jsend\JsendResponse;
use BackToWin\Framework\Jsend\JsendSuccessResponse;

class GetController
{
    /**
     * @var CommandBus
     */
    private $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(string $userId): JsendResponse
    {
        try {
            $avatar = $this->bus->execute(new GetAvatarCommand($userId));

            return new JsendSuccessResponse([
                'avatar' => $avatar
            ]);
        } catch (NotFoundException | \InvalidArgumentException $e) {
            return (new JsendFailResponse([
                new JsendError($e->getMessage())
            ]))->withStatus(404);
        }
    }
}
