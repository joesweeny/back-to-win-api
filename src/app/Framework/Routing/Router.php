<?php

namespace GamePlatform\Framework\Routing;

use FastRoute\RouteCollector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Router implements RequestHandlerInterface
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
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->mappers as $mapper) {
                $mapper->map($r);
            }
        });

        return $this->dispatcher->dispatch($request, $dispatcher);
    }
}
