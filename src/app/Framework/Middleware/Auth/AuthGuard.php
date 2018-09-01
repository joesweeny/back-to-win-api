<?php

namespace GamePlatform\Framework\Middleware\Auth;

use Chief\CommandBus;
use GamePlatform\Bootstrap\Config;
use GamePlatform\Boundary\Auth\Command\ValidateTokenCommand;
use GamePlatform\Framework\Exception\BadRequestException;
use GamePlatform\Framework\Exception\NotAuthenticatedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthGuard implements MiddlewareInterface
{
    /**
     * @var CommandBus
     */
    private $bus;
    /**
     * @var Config
     */
    private $config;

    public function __construct(CommandBus $bus, Config $config)
    {
        $this->bus = $bus;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     * @throws BadRequestException
     * @throws NotAuthenticatedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isExempt($this->parseMethod($request), $this->parsePath($request))) {
            return $handler->handle($request);
        }

        $token = $this->parseAuthToken($request);

        $this->bus->execute(new ValidateTokenCommand($token));

        return $handler->handle($request);
    }

    private function isExempt(string $method, string $path): bool
    {
        foreach ($this->config->get('auth.exempt-paths') as $key => $value) {
            return $key === $method && $value === $path;
        }

        return false;
    }

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
        return $request->getMethod();
    }

    private function parsePath(ServerRequestInterface $request): string
    {
        return $request->getUri()->getPath();
    }
}
