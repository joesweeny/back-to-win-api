<?php

namespace GamePlatform\Framework\Middleware\Error;

use GamePlatform\Framework\Jsend\JsendErrorResponse;
use Psr\Http\Message\ResponseInterface;

class JsonErrorResponseFactory implements ErrorResponseFactory
{
    /**
     * @inheritdoc
     */
    public function create(\Throwable $exception): ResponseInterface
    {
        return new JsendErrorResponse();
    }
}
