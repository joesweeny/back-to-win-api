<?php

namespace GamePlatform\Framework\Middleware\Auth;

use Chief\CommandBus;
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

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @inheritdoc
     * @throws BadRequestException
     * @throws NotAuthenticatedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $this->parseAuthToken($request);

        $this->bus->execute(new ValidateTokenCommand($token));

        return $handler->handle($request);
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
}
