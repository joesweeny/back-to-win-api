<?php

namespace GamePlatform\Application\Http;

use GamePlatform\Framework\Middleware\Auth\AuthGuard;
use GamePlatform\Framework\Middleware\Entity\EntityGuard;
use GamePlatform\Framework\Middleware\Error\ErrorHandler;
use Interop\Container\ContainerInterface;
use GamePlatform\Framework\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Stratigility\MiddlewarePipe;
use function Zend\Stratigility\path;

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

        $pipe->pipe(path('/', $this->container->get(ErrorHandler::class)));
        $pipe->pipe(path('/api', $this->container->get(AuthGuard::class)));
        $pipe->pipe(path('/api', $this->container->get(EntityGuard::class)));

        return $pipe->process($request, $this->container->get(Router::class));
    }
}
