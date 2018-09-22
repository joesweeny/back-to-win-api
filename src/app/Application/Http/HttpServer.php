<?php

namespace BackToWin\Application\Http;

use BackToWin\Framework\Middleware\Auth\AuthGuard;
use BackToWin\Framework\Middleware\Entity\EntityGuard;
use BackToWin\Framework\Middleware\Error\ErrorHandler;
use Interop\Container\ContainerInterface;
use BackToWin\Framework\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Stratigility\Middleware\PathMiddlewareDecorator;
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

        $pipe->pipe(new PathMiddlewareDecorator('/', $this->container->get(ErrorHandler::class)));
        $pipe->pipe(new PathMiddlewareDecorator('/api', $this->container->get(AuthGuard::class)));
        $pipe->pipe(new PathMiddlewareDecorator('/api', $this->container->get(EntityGuard::class)));

        return $pipe->process($request, $this->container->get(Router::class));
    }
}
