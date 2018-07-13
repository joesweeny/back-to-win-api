<?php

namespace BackToWin\Framework\Routing;

use FastRoute\Dispatcher as FastRouteDispatcher;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
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
     * @param $dispatcher
     * @return \Psr\Http\Message\ResponseInterface|HtmlResponse
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
                $vars = $routeInfo[2];

                $instance = $this->container->get($controller);
                /** @var \Psr\Http\Message\ResponseInterface $response */
                $response = call_user_func_array([$instance, $method], [$request]);

                return $response;
                break;

            case FastRouteDispatcher::METHOD_NOT_ALLOWED:
            case FastRouteDispatcher::NOT_FOUND:
            default:
                return new HtmlResponse('404 - Page not foud', 404);
                break;

        }
    }
}
