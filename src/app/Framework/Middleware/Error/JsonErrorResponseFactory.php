<?php

namespace GamePlatform\Framework\Middleware\Error;

use GamePlatform\Framework\Exception\BadRequestException;
use GamePlatform\Framework\Exception\NotAuthenticatedException;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Jsend\JsendError;
use GamePlatform\Framework\Jsend\JsendErrorResponse;
use GamePlatform\Framework\Jsend\JsendFailResponse;
use Psr\Http\Message\ResponseInterface;

class JsonErrorResponseFactory implements ErrorResponseFactory
{
    /**
     * @inheritdoc
     */
    public function create(\Throwable $exception): ResponseInterface
    {
        if ($exception instanceof BadRequestException) {
            return (new JsendFailResponse([
                new JsendError($exception->getMessage() ?: 'Bad Request', 400)
            ]))->withStatus(400);
        }

        if ($exception instanceof NotAuthenticatedException) {
            return (new JsendFailResponse([
                new JsendError($exception->getMessage() ?: 'Not Authenticated', 403)
            ]))->withStatus(403);
        }

        if ($exception instanceof NotFoundException) {
            return (new JsendFailResponse([
                new JsendError($exception->getMessage() ?: 'Not Found', 404)
            ]))->withStatus(404);
        }

        return new JsendErrorResponse();
    }
}
