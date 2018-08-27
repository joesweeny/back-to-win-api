<?php

namespace GamePlatform\Framework\Middleware\Error;

use Psr\Http\Message\ResponseInterface;

interface ErrorResponseFactory
{
    /**
     * @param \Throwable $exception
     * @return ResponseInterface
     */
    public function create(\Throwable $exception): ResponseInterface;
}
