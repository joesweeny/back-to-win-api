<?php

namespace GamePlatform\Framework\Routing;

use FastRoute\RouteCollector;
use Interop\Http\Middleware\DelegateInterface;
use Psr\Http\Message\RequestInterface;

class Router implements DelegateInterface
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var array|RouteMapper[]
     */
    private $mappers = [];

    /**
     * Router constructor.
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function addRoutes(RouteMapper $mapper): Router
    {
        $this->mappers[] = $mapper;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request)
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->mappers as $mapper) {
                $mapper->map($r);
            }
        });

        return $this->dispatcher->dispatch($request, $dispatcher);
    }
}
