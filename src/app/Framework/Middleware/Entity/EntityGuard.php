<?php

namespace GamePlatform\Framework\Middleware\Entity;

use GamePlatform\Framework\Request\RequestBuilder;
use GamePlatform\Framework\Exception\BadRequestException;
use GamePlatform\Framework\Exception\NotAuthenticatedException;
use Lcobucci\JWT\Parser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EntityGuard implements MiddlewareInterface
{
    private $guardedRoutes = [
        'PUT' =>  '/avatar',
    ];
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @inheritdoc
     * @throws BadRequestException
     * @throws NotAuthenticatedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isRouteGuarded($this->parseMethod($request), $this->parsePath($request))) {
            $body = json_decode($request->getBody()->getContents());

            if (!$userId = ($body->user_id ?? null)) {
                throw new BadRequestException("Required field 'user_id' is missing");
            }

            $token = $this->parser->parse($this->parseAuthToken($request));

            if ($userId !== $token->getClaim('user_id')) {
                throw new NotAuthenticatedException('You are not authenticated to update this resource');
            }

            return $handler->handle(RequestBuilder::rebuildRequest($request, json_encode($body) ?: ''));
        }

        return $handler->handle($request);
    }

    private function isRouteGuarded(string $method, string $path): bool
    {
        foreach ($this->guardedRoutes as $key => $value) {
            return $key === $method && $value === $path;
        }

        return false;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws BadRequestException
     */
    private function parseAuthToken(ServerRequestInterface $request): string
    {
        if (!$header = $request->getHeaderLine('Authorization')) {
            throw new BadRequestException('Authorization header missing from Request');
        }

        if (0 !== strpos($header, 'Bearer')) {
            throw new BadRequestException("Authorization header value '{$header}' is not in the correct format");
        }

        return trim(substr($header, 7));
    }

    private function parseMethod(ServerRequestInterface $request): string
    {
        return strtoupper($request->getMethod());
    }

    private function parsePath(ServerRequestInterface $request): string
    {
        return $request->getUri()->getPath();
    }
}
