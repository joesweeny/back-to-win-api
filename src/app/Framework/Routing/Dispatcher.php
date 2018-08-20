<?php

namespace GamePlatform\Framework\Routing;

use GamePlatform\Framework\Exception\NotFoundException;
use FastRoute\Dispatcher as FastRouteDispatcher;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class Dispatcher
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Router constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param RequestInterface $request
     * @param  FastRouteDispatcher $dispatcher
     * @return \Psr\Http\Message\ResponseInterface|HtmlResponse
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request, FastRouteDispatcher $dispatcher)
    {
        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case FastRouteDispatcher::FOUND:
                $target = $routeInfo[1];
                $bits = explode('@', $target);
                $controller = $bits[0];
                $method = $bits[1] ?? '__invoke';
                $params = $routeInfo[2];

                $instance = $this->container->get($controller);

                $params[] = $request;
                $response = \call_user_func_array([$instance, $method], $params);

                if (!$response instanceof ResponseInterface) {
                    throw new \RuntimeException("Return value of $controller::$method is not an instance of " . ResponseInterface::class);
                }

                return $response;
            case FastRouteDispatcher::METHOD_NOT_ALLOWED:
            case FastRouteDispatcher::NOT_FOUND:
                throw new NotFoundException('Invalid route');
            default:
                throw new \RuntimeException("Unexpected dispatcher code returned: {$routeInfo[0]}");
        }
    }
}
