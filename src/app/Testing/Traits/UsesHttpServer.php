<?php

namespace BackToWin\Testing\Traits;

use Interop\Container\ContainerInterface;
use BackToWin\Application\Http\HttpServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait UsesHttpServer
{
    /**
     * @param ContainerInterface $container
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    protected function handle(ContainerInterface $container, ServerRequestInterface $request): ResponseInterface
    {
        return $container->get(HttpServer::class)->handle($request);
    }
}
