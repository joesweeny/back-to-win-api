<?php

namespace GamePlatform\Application\Http;

use GamePlatform\Framework\Middleware\Error\ErrorHandler;
use Interop\Container\ContainerInterface;
use GamePlatform\Framework\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSR7Session\Http\SessionMiddleware;
use Zend\Diactoros\Response;
use Zend\Stratigility\Middleware\CallableMiddlewareWrapper;
use Zend\Stratigility\MiddlewarePipe;

class HttpServer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * HttpServer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Handle a HTTP request and return an HTTP response.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $pipe = new MiddlewarePipe;

        $pipe->raiseThrowables();

        return $pipe
            ->pipe('/', $this->container->get(ErrorHandler::class))

            ->process($request, $this->container->get(Router::class));
    }
}
