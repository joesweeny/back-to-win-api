<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Avatar;

use Chief\CommandBus;
use GamePlatform\Boundary\Avatar\Command\GetAvatarCommand;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Jsend\JsendError;
use GamePlatform\Framework\Jsend\JsendFailResponse;
use GamePlatform\Framework\Jsend\JsendResponse;
use GamePlatform\Framework\Jsend\JsendSuccessResponse;

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
