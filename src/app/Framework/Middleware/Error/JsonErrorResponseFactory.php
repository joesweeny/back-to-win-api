<?php

namespace BackToWin\Framework\Middleware\Error;

use BackToWin\Framework\Exception\BadRequestException;
use BackToWin\Framework\Exception\NotAuthenticatedException;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Jsend\JsendError;
use BackToWin\Framework\Jsend\JsendErrorResponse;
use BackToWin\Framework\Jsend\JsendFailResponse;
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
